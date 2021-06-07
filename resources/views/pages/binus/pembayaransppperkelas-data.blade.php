<x-app-layout>
    <x-slot name="header_content">
        <h1>{{ __('Data Pembayaran SPP Per Kelas') }}</h1>

        <div class="section-header-breadcrumb">
        <div class="breadcrumb-item active"><a href="{{ route('dashboard') }}">Dashboard</a></div>
            <div class="breadcrumb-item"><a href="#">Tata Usaha</a></div>
            <div class="breadcrumb-item"><a href="{{ route('pembayaransppperkelasw') }}">Data Pembayaran SPP Per Kelas</a></div>
        </div>
    </x-slot>

    <div>
        <livewire:table.tablepembayaransppperkelas name="pembayaransppperkelas" :model="$pembayaransppperkelas" />
    </div>
</x-app-layout>
