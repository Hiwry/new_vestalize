<?php

namespace App\Imports;

use App\Models\FabricPiece;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class FabricPiecesImport implements ToModel, WithHeadingRow
{
    public function model(array $row)
    {
        // Skip empty rows
        if (empty($row['chave_de_acesso'])) {
            return null;
        }

        // Parse Date (Emissão)
        $receivedAt = null;
        try {
            if (!empty($row['emissao'])) {
                // Excel dates usually come as extensive integers, but this library might convert them
                // Adjust format based on user input, assuming d/m/Y H:i:s or standard Excel date
                $receivedAt = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($row['emissao']);
            }
        } catch (\Exception $e) {
            $receivedAt = now();
        }

        // Invoice Number logic (Série/Nu)
        $invoiceNumber = $row['serienu'] ?? null;

        return new FabricPiece([
            'store_id' => 1, // Default store, user can adjust later
            'fabric_id' => 1, // Default fabric
            'fabric_type_id' => null,
            'color_id' => 1, // Default color
            'status' => 'fechada',
            
            // Mapped Fields
            'received_at' => $receivedAt,
            'invoice_key' => $row['chave_de_acesso'] ?? null,
            'invoice_number' => $invoiceNumber,
            'purchase_price' => $row['valor'] ?? 0,
            
            // Extra info in notes
            'notes' => 'Importado via Excel. Comprador: ' . ($row['comprador'] ?? 'N/A') . '. Origem: ' . ($row['razao_social_emissor'] ?? 'N/A'),
            
            // Defaults
            'weight' => 0,
            'weight_current' => 0,
            'meters' => 0,
            'supplier' => $row['razao_social_emissor'] ?? null,
        ]);
    }
}
