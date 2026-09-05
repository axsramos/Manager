<?php

namespace App\Controls\Menu;

use App\Class\Auth\AuthClass;
use App\Core\Config;
use App\Core\AuthSession;
use App\Models\CAS\CasMnuModel;
use App\Models\CAS\CasMnaModel;

class SideMenuControls
{
    public static function getSideMenu(): string
    {
        /**
         * Static Menu
         */
        $dataContent = '';
        $path = Config::$DIR_BASE . '/App/Static/Menu/SideMenu.json';

        if (file_exists($path)) {
            $dataContent = file_get_contents($path);
        }

        /**
         * Permissions
         */
        $data = self::applyPermissions($dataContent);
        $dataContent = json_encode($data);

        return $dataContent;
    }

    private static function applyPermissions(string $dataContent): object
    {
        $data = json_decode($dataContent);

        /**
         * Remove PORTAL SITI menu if user is not ADMINISTRADOR PORTALSITI
         * This is a security measure to prevent unauthorized access to the portal menu.
         * ADMINISTRADOR PORTALSITI profile is exclusive to the SITI Portal.
         */
        $isRemove = true;
        if (isset(AuthSession::get()['PROFILE'])) {
            if (in_array(strtoupper(\App\Core\AuthSession::get()['PROFILE']), array_merge(Config::getTypeUsersAdminPortal(), Config::getTypeUsersSupportPortal()))) {
                $isRemove = false;
            }
        }
        if ($isRemove) {
            unset($data->SideMenu[1]); // Menu PORTAL SITI //
        }

        /**
         * Remove CONTROLE DE ACESSO menu if user is not MANAGER ACCOUNT
         * This is a security measure to prevent unauthorized access to the portal access control.
         * The MANAGER ACCOUNT profile is common to the user who owns the repository itself.
         */
        $isRemove = true;
        if (isset(AuthSession::get()['PROFILE'])) {
            // if (in_array(strtoupper(\App\Core\AuthSession::get()['PROFILE']), array_merge(\App\Core\Config::getTypeUsersSupportPortal(), \App\Core\Config::getTypeUsersAdminLocal()))) {
            if (in_array(strtoupper(\App\Core\AuthSession::get()['PROFILE']), Config::getTypeUsersSupportPortal())) {
                $isRemove = false;
            }
        }
        if ($isRemove) {
            unset($data->SideMenu[2]); // Menu CONTROLE DE ACESSO //
        }

        /**
         * Build dinamic menu
         */
        if (AuthSession::get()['USR_LOGGED'] !== 'anonymous') {
            $data = self::mountMenu((array) $data);
        }

        return $data;
    }

    private static function loadMenuWithPermissions(): array
    {
        $menuTree = array();

        $rps_id = (AuthSession::get()['RPS_ID'] ?? '');

        $obCasMnuModel = new CasMnuModel();
        $obCasMnuModel->CasRpsCod = $rps_id;
        $obCasMnuModel->setSelectedFields(['CasRpsCod', 'CasMnuCod']);

        if ($obCasMnuModel->readAllLinesJoin(['CasRps'])) {
            foreach ($obCasMnuModel->getRecords() as $menuItem) {
                $obCasMnaModel = new CasMnaModel();
                $obCasMnaModel->CasRpsCod = $menuItem['CasRpsCod'];
                $obCasMnaModel->CasMnuCod = $menuItem['CasMnuCod'];
                $obCasMnaModel->setSelectedFields(['CasRpsCod', 'CasMnuCod', 'CasMnuDsc', 'CasMnuGrp', 'CasMnuBlq', 'CasMnaCod', 'CasPrgCod', 'CasMnaDsc', 'CasMnaBlq', 'CasMnaLnk', 'CasMnaIco', 'CasMnaGrp']);
                if ($obCasMnaModel->readAllLinesJoin(['CasRps', 'CasMnu'])) {
                    foreach ($obCasMnaModel->getRecords() as $mnaItem) {
                        if ($mnaItem['CasPrgCod'] === '_blank') {
                            $menuTree[$menuItem['CasMnuCod']][$mnaItem['CasMnaCod']] = $mnaItem;
                        } else {
                            $obAuthClass = new AuthClass();
                            $dataPermissions = $obAuthClass->permissions($mnaItem['CasPrgCod'], false);
                            if (in_array('AUTHORIZED', $dataPermissions)) {
                                $menuTree[$menuItem['CasMnuCod']][$mnaItem['CasMnaCod']] = $mnaItem;
                            }
                        }
                    }
                }
            }
        }

        return $menuTree;
    }

    private static function mountMenu(array $data): object
    {
        $menuTree = self::loadMenuWithPermissions();

        foreach ($menuTree as $menuItem) {
            $groups = array();
            foreach ($menuItem as $menuSubItem) {
                if ($menuSubItem['CasMnaDsc'] != $menuSubItem['CasMnaGrp']) {
                    array_push($groups, $menuSubItem['CasMnaGrp']);
                }
            }
            $groups = array_values(array_unique($groups));

            if ($groups) {
                // module and group //
                foreach ($groups as $keyGroup => $group) {
                    $idx = 0;
                    foreach ($menuItem as $menuSubItem) {
                        if ($menuSubItem['CasMnaGrp'] == $group) {
                            if (! isset($data['SideMenu'][$menuSubItem['CasMnuDsc']]['Description'])) {
                                // module //
                                $data['SideMenu'][$menuSubItem['CasMnuDsc']] = array(
                                    'Description' => $menuSubItem['CasMnuDsc'],
                                    'Items' => array()
                                );
                            }
                            // group //
                            $add = true;
                            foreach ($data['SideMenu'][$menuSubItem['CasMnuDsc']]['Items'] as $item) {
                                if ($item['Description'] == $menuSubItem['CasMnaGrp']) {
                                    $add = false;
                                    break;
                                }
                            }
                            if ($add) {
                                $data['SideMenu'][$menuSubItem['CasMnuDsc']]['Items'][] = array(
                                    'Description' => $menuSubItem['CasMnaGrp'],
                                    'Icon' => $menuSubItem['CasMnaIco'],
                                    'Link' => $menuSubItem['CasMnaLnk'],
                                    "Anchor" => $menuSubItem['CasMnaGrp'],
                                    'SubItems' => array()
                                );
                            }
                            // item //
                            $data['SideMenu'][$menuSubItem['CasMnuDsc']]['Items'][$keyGroup]['SubItems'][$idx] = array(
                                "Description" => $menuSubItem['CasMnaDsc'],
                                "Icon" => $menuSubItem['CasMnaIco'],
                                "Link" => $menuSubItem['CasMnaLnk'],
                                "Anchor" => $menuSubItem['CasMnaDsc'],
                                "SubItems" => array()
                            );

                            // subitem //
                            // todo... //
                            //
                            $idx++;
                        }
                    }
                }
            } else {
                foreach ($menuItem as $menuSubItem) {
                    if (isset($data['SideMenu'][$menuSubItem['CasMnuDsc']])) {
                        $data['SideMenu'][$menuSubItem['CasMnuDsc']]['Items'][] = array(
                            "Description" => $menuSubItem['CasMnaDsc'],
                            "Icon" => $menuSubItem['CasMnaIco'],
                            "Link" => $menuSubItem['CasMnaLnk'],
                            "SubItems" => array()
                        );
                        continue;
                    }
                    // first element //
                    $data['SideMenu'][$menuSubItem['CasMnuDsc']] = array(
                        "Description" => $menuSubItem['CasMnuDsc'],
                        "Items" => array(
                            array(
                                "Description" => $menuSubItem['CasMnaDsc'],
                                "Icon" => $menuSubItem['CasMnaIco'],
                                "Link" => $menuSubItem['CasMnaLnk'],
                                "SubItems" => array()
                            ),
                        )
                    );
                }
            }
        }

        return (object) $data;
    }
}
