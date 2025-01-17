<?php

// Fields of record types to be included in the export
return [
    'pages' => [
        'title',
        'deleted',
        'doktype',
        'hidden',
        'perms_everybody',
    ],
    'tt_content' => [
        'CType',
        'header',
        'deleted',
        'hidden',
        't3ver_oid',
        'tx_irretutorial_1nff_hotels',
        'tx_testirrecsv_hotels',
    ],
    'tx_testirrecsv_hotel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'offers',
    ],
    'tx_testirrecsv_offer' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'prices',
    ],
    'tx_testirrecsv_price' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'price',
    ],
    'tx_irretutorial_1nff_hotel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'parentid',
        'parenttable',
        'parentidentifier',
        'title',
        'offers',
    ],
    'tx_irretutorial_1nff_offer' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'parentid',
        'parenttable',
        'parentidentifier',
        'title',
        'prices',
    ],
    'tx_irretutorial_1nff_price' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'parentid',
        'parenttable',
        'parentidentifier',
        'title',
        'price',
    ],
    'tx_irretutorial_mnasym_hotel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'offers',
    ],
    'tx_irretutorial_mnasym_hotel_offer_rel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'deleted',
        'hidden',
        'hotelid',
        'offerid',
        'hotelsort',
        'offersort',
        'prices',
    ],
    'tx_irretutorial_mnasym_offer' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'hotels',
    ],
    'tx_irretutorial_mnasym_price' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'parentid',
        'title',
        'price',
    ],
    'tx_irretutorial_mnattr_hotel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'offers',
    ],
    'tx_irretutorial_mnattr_hotel_offer_rel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'deleted',
        'hidden',
        'hotelid',
        'offerid',
        'hotelsort',
        'offersort',
        'quality',
        'allincl',
    ],
    'tx_irretutorial_mnattr_offer' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'hotels',
    ],
    'tx_irretutorial_mnmmasym_hotel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'offers',
    ],
    'tx_irretutorial_mnmmasym_hotel_offer_rel' => [
        'uid_local',
        'uid_foreign',
        'tablenames',
        'sorting',
        'sorting_foreign',
        'ident',
    ],
    'tx_irretutorial_mnmmasym_offer' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'hotels',
        'prices',
    ],
    'tx_irretutorial_mnmmasym_offer_price_rel' => [
        'uid_local',
        'uid_foreign',
        'tablenames',
        'sorting',
        'sorting_foreign',
        'ident',
    ],
    'tx_irretutorial_mnmmasym_price' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'price',
        'offers',
    ],
    'tx_irretutorial_mnsym_hotel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'sorting',
        'deleted',
        'hidden',
        'title',
        'branches',
    ],
    'tx_irretutorial_mnsym_hotel_rel' => [
        'cruser_id',
        'sys_language_uid',
        'l18n_parent',
        'deleted',
        'hidden',
        'hotelid',
        'branchid',
        'hotelsort',
        'branchsort',
    ],
];
