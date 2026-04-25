<?php

namespace App\Console\Commands;

use App\Models\Category;
use App\Models\MenuFooter;
use App\Models\MenuHeader;
use App\Models\Tree;
use Illuminate\Console\Command;

class FixTree extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tree:fix';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix and rebuild product categories';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        //if ($res = Category::fixTree()) {
        Category::fixTree();
        Category::fixDepthTree();
        Tree::fixTree();
        Tree::fixDepthTree();
        MenuHeader::fixTree();
        MenuHeader::fixDepthTree();
        MenuFooter::fixTree();
        MenuFooter::fixDepthTree();
        return Command::SUCCESS;
    }
}
