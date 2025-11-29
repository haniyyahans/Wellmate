<?php

require_once __DIR__ . '/../config/database.php';

class TrackingModel {
    
    /**
     * Hitung kebutuhan cairan harian
     */
    public function hitungTracking($beratBadan) {
        if (!is_numeric($beratBadan) || $beratBadan <= 0) {
            return [
                'success' => false,
                'message' => 'Berat badan harus berupa angka positif',
                'data' => null
            ];
        }
        
        $kebutuhanCairanML = $beratBadan * 35;
        $kebutuhanCairanLiter = $kebutuhanCairanML / 1000;
        $jumlahGelas = ceil($kebutuhanCairanML / 250);
        
        return [
            'success' => true,
            'message' => 'Perhitungan berhasil',
            'data' => [
                'berat_badan' => (float) $beratBadan,
                'kebutuhan_cairan_ml' => (int) round($kebutuhanCairanML, 0),
                'kebutuhan_cairan_liter' => (float) round($kebutuhanCairanLiter, 2),
                'jumlah_gelas' => (int) $jumlahGelas
            ]
        ];
    }
}