<?php

namespace App\Class\Pattern;

class FormDesign
{
    public const OPTION_TABSMODEL = ['Default', 'Single', 'SearchRegisterReport'];
    public const TRANSACTION_MODE = ['Display', 'Insert', 'Update', 'Delete'];

    public static function standard(string $module, string  $program, string $breadCrumbs = ''): array
    {
        return array(
            'Module' => strtoupper($module) . ' > ',
            'Program' => $program,
            'BreadCrumbs' => $breadCrumbs,
            'Message' => self::secMessage(),
            'Styles' => self::secStyles(),
            'Scripts' => self::secScripts(),
        );
    }

    public static function withSideBarAdmin(string $module, string $program, string $breadCrumbs, string $dataTopMenu, string $dataSideMenu): array
    {
        return array(
            'Module' => strtoupper($module) . ' > ',
            'Program' => $program,
            'BreadCrumbs' => $breadCrumbs,
            'Message' => self::secMessage(),
            'Styles' => self::secStyles(),
            'Scripts' => self::secScripts(),
            'UserMenu' => $dataTopMenu,
            'SideMenu' => $dataSideMenu,
            'TransMode' => self::TRANSACTION_MODE[0],
            'Fields' => array(),
            'FormDisable' => false,
        );
    }

    public static function withTabs(string $module, string $program, string $breadCrumbs, string $dataTopMenu, string $dataSideMenu): array
    {
        $dataReturn = self::withSideBarAdmin($module, $program, $breadCrumbs, $dataTopMenu, $dataSideMenu);

        $dataReturn['Tabs'] = array(
            'Template' => 'withTabs',
            'Items' => self::tabsModel(),
            'Current' => 0,
            'LoadFile' => '', // app/views/{ModuleAndProgram}.php //
        );

        return $dataReturn;
    }

    public static function secMessage(int $code = 0, string $type = 'warning', string $title = 'Atenção', string $description = '', bool $modal = true): array
    {
        return array(
            'Code' => $code,
            'Type' => $type,
            'Title' => $title,
            'Description' => $description,
            'WithModal' => $modal,
        );
    }

    private static function secScripts(): array
    {
        return array(
            'Head' => array(),
            'Body' => array(),
        );
    }

    private static function secStyles(): array
    {
        return array(
            'CSSFiles' => array()
        );
    }

    public static function tabsModel(string $model = self::OPTION_TABSMODEL[0], string $baseLinkProgram = '#'): array
    {
        $dataReturn = array();

        switch ($model) {
            case self::OPTION_TABSMODEL[1]:
                // 'Single'
                array_push($dataReturn, array(
                    'Name' => 'Geral',
                    'Link' => $baseLinkProgram,
                    'IsDisabled' => false,
                ));
                break;

            case self::OPTION_TABSMODEL[2]:
                // 'SearchRegisterReport'
                array_push($dataReturn, array(
                    'Name' => 'Consulta',
                    'Link' => $baseLinkProgram,
                    'IsDisabled' => false,
                ));
                array_push($dataReturn, array(
                    'Name' => 'Cadastro',
                    'Link' => $baseLinkProgram . '/Show/{id}',
                    'IsDisabled' => false,
                ));
                array_push($dataReturn, array(
                    'Name' => 'Relatório',
                    'Link' => $baseLinkProgram . '/Report/{id}',
                    'IsDisabled' => true,
                ));
                break;

            default:
                array_push($dataReturn, array(
                    'Name' => 'Consulta',
                    'Link' => $baseLinkProgram,
                    'IsDisabled' => false,
                ));
                array_push($dataReturn, array(
                    'Name' => 'Cadastro',
                    'Link' => $baseLinkProgram . '/Show/{id}',
                    'IsDisabled' => false,
                ));
                break;
        }

        return $dataReturn;
    }
}
