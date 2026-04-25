<?php

namespace App\Console\Commands;

class SalesDriveProducts extends XmlParserCommand
{
    protected $signature = 'import_products:sales_drive';

    protected $description = 'import products data from Sales Drive';

    protected $urlExportLink;

    public function __construct()
    {
        $this->urlExportLink = setting('export-link-sales-drive');

        parent::__construct();
    }
}
