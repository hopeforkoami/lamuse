<?php

namespace App\Services;

use Dompdf\Dompdf;
use Dompdf\Options;

class PdfService
{
    public function generateSalesReport($artist, $songs, $totalSales)
    {
        $options = new Options();
        $options->set('isHtml5ParserEnabled', true);
        $dompdf = new Dompdf($options);

        $html = "<html><body>";
        $html .= "<h1>Rapport de Ventes - {$artist->name}</h1>";
        $html .= "<p>Date: " . date('d/m/Y') . "</p>";
        $html .= "<h2>Résumé des morceaux</h2>";
        $html .= "<table border='1' width='100%' cellpadding='10'>";
        $html .= "<thead><tr><th>Morceau</th><th>Prix</th><th>Genre</th></tr></thead><tbody>";
        
        foreach ($songs as $song) {
            $html .= "<tr><td>{$song->title}</td><td>{$song->price} {$song->currency_code}</td><td>{$song->genre}</td></tr>";
        }
        
        $html .= "</tbody></table>";
        $html .= "<h3>Total des ventes estimées: {$totalSales} XOF</h3>";
        $html .= "</body></html>";

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        return $dompdf->output();
    }
}
