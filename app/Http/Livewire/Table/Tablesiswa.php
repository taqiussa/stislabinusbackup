<?php

namespace App\Http\Livewire\Table;

use App\Models\Kelas;
use App\Models\Keterangan;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Siswa;

class Tablesiswa extends Component
{
    use WithPagination;

    public $model;
    public $name;
    public $idsiswa;
    public $nis;
    public $nama;
    public $idkelas;
    public $tahun;
    public $ortu;
    public $keterangan_id;
    public $simpan = 'Simpan';

    public $isOpen = 0;
    public $perPage = 10;
    public $sortField = "idkelas";
    public $sortKelas = '';
    public $sortTahun = '';
    public $sortAsc = false;
    public $search = '';
    public $action;
    public $button;

    protected $listeners = ["deleteItem" => "delete_item"];
    protected $rules = [
        'nis' => 'required',
        'nama' => 'required',
        'idkelas' => 'required',
        'tahun' => 'required',
    ];
    protected $messages = [
        'nis.required' => 'Isi NIS',
        'nama.required' => 'Isi nama',
        'idkelas.required' => 'Pilih Kelas',
        'tahun.required' => 'Isi Tahun',
    ];

    public function store()
    {
        $data = [
            'nis' => $this->nis,
            'nama' => $this->nama,
            'idkelas' => $this->idkelas,
            'tahun' => $this->tahun,
            'ortu' => $this->ortu,
            'ket' => '',
            'keterangan_id' => $this->keterangan_id,
        ];

        $this->validate();
        Siswa::updateOrCreate(['id' => $this->idsiswa], $data);
        $this->resetErrorBag();
        $this->clearVar();
        $this->emit('saved');
        $this->hideModal();
    }

    public function edit($id)
    {
        $cari = Siswa::findOrFail($id);
        $this->idsiswa = $id;
        $this->nis = $cari->nis;
        $this->nama = $cari->nama;
        $this->idkelas = $cari->idkelas;
        $this->tahun = $cari->tahun;
        $this->ortu = $cari->ortu;
        $this->keterangan_id = $cari->keterangan_id;
        $this->simpan = 'Update';
        $this->button = create_button('update', "Siswa");
        $this->showModal();
    }

    public function showModal()
    {
        $this->isOpen = true;
    }

    public function hideModal()
    {
        $this->resetErrorBag();
        $this->clearVar();
        $this->isOpen = false;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortAsc = !$this->sortAsc;
        } else {
            $this->sortAsc = true;
        }

        $this->sortField = $field;
    }

    public function get_pagination_data()
    {
        switch ($this->name) {
            case 'siswa':
                $siswas = $this->model::search($this->search)
                    ->join('kelas', 'kelas.id', '=', 'siswa.idkelas')
                    ->select(
                        'siswa.id as id',
                        'siswa.nama as nama',
                        'siswa.nis as nis',
                        'siswa.tahun as tahun',
                        'siswa.ortu as ortu',
                        'siswa.keterangan_id as keterangan_id',
                        'siswa.idkelas as idkelas',
                        'kelas.tingkat as tingkat',
                        'kelas.jurusan as jurusan',
                    )
                    ->orderBy($this->sortField, $this->sortAsc ? 'asc' : 'desc')
                    ->orderBy('nama', 'asc')
                    ->where('idkelas', $this->sortKelas)
                    ->where('tahun', $this->sortTahun)
                    ->paginate($this->perPage);
                return [
                    "view" => 'livewire.table.siswa',
                    "siswas" => $siswas,
                    'kelas' => Kelas::orderBy('tingkat')->orderBy('jurusan')->get(),
                    'keter' => Keterangan::orderBy('ket', 'asc')->get(),
                    "data" => array_to_object([
                        'href' => [
                            'create_new' => 'showModal()',
                            'create_new_text' => 'Buat siswa',
                            'export' => '#',
                            'export_text' => 'Import',
                            'kelase' => Kelas::orderBy('tingkat')->orderBy('jurusan')->get(),
                            'tahune' => Siswa::select('tahun')->distinct()->orderBy('tahun', 'asc')->get(),
                        ]
                    ])
                ];
                break;

            default:
                # code...
                break;
        }
    }

    public function delete_item($id)
    {
        $data = $this->model::find($id);

        if (!$data) {
            $this->emit("deleteResult", [
                "status" => false,
                "message" => "Gagal menghapus data " . $this->name
            ]);
            return;
        }

        $data->delete();
        $this->emit("deleteResult", [
            "status" => true,
            "message" => "Data " . $this->name . " berhasil dihapus!"
        ]);
    }

    public function clearVar()
    {
        $this->idsiswa = '';
        $this->nama = '';
        $this->idkelas = '';
        $this->ortu = '';
        $this->tahun = '';
        $this->keterangan_id = '';
        $this->nis = '';
        $this->simpan = 'Simpan';
        $this->button = create_button($this->action, "siswa Baru");
    }

    public function mount()
    {
        $this->button = create_button($this->action, "siswa Baru");
        // this button untuk menampilkan emit atau message toast 

    }

    public function render()
    {
        $data = $this->get_pagination_data();
        return view($data['view'], $data);
    }
}
