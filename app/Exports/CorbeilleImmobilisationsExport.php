<?php

namespace App\Exports;

use App\Models\CorbeilleImmobilisation;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class CorbeilleImmobilisationsExport implements FromCollection, WithHeadings, WithMapping
{
    public function __construct(
        private readonly ?string $search = null
    ) {
    }

    public function collection(): Collection
    {
        $query = CorbeilleImmobilisation::query()->orderByDesc('id');

        if ($this->search) {
            $query->where(function ($q): void {
                $q->where('designation_label', 'like', '%' . $this->search . '%')
                    ->orWhere('original_num_ordre', 'like', '%' . $this->search . '%')
                    ->orWhere('idDesignation', 'like', '%' . $this->search . '%');
            });
        }

        return $query->get();
    }

    public function headings(): array
    {
        return [
            'Corbeille ID',
            'NumOrdre original',
            'Designation',
            'idDesignation',
            'idCategorie',
            'idEtat',
            'idEmplacement',
            'idNatJur',
            'idSF',
            'DateAcquisition',
            'Observations',
            'Barcode',
            'Raison suppression',
            'Supprime par',
            'Supprime le',
        ];
    }

    public function map($row): array
    {
        return [
            $row->id,
            $row->original_num_ordre,
            $row->designation_label,
            $row->idDesignation,
            $row->idCategorie,
            $row->idEtat,
            $row->idEmplacement,
            $row->idNatJur,
            $row->idSF,
            optional($row->DateAcquisition)?->format('Y-m-d'),
            $row->Observations,
            $row->barcode,
            $row->deleted_reason,
            $row->deleted_by_user_id,
            optional($row->deleted_at)?->format('Y-m-d H:i:s'),
        ];
    }
}
