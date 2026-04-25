<?php

use App\Models\Group;
use Illuminate\Database\Seeder;

class ManagerGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * php artisan db:seed --class=ManagerGroupSeeder
     *
     * @return void
     */
    public function run()
    {
        Group::create([
            'slug' => 'manager',
            'name' => 'Менеджер',
            'permissions' => json_decode('{"ordersall.view":true,"orders.view":true,"ordersquick.view":true,"admin.access":true,"dashboard.view":false,"tree.view":false,"menufooterheader.view":false,"menuheader.view":false,"menufooter.view":false,"news.view":false,"tags.view":false,"money.view":false,"products.view":false,"categories.view":false,"labels.view":false,"brands.view":false,"productstatus.view":false,"characteristicsmenu.view":false,"characteristics.view":false,"characteristicoptions.view":false,"feedback.view":false,"comments.view":false,"subscriptions.view":false,"slidermains.view":false,"imagestorage.view":false,"imagestorageimages.view":false,"settingsblock.view":false,"settingssettingsall.view":false,"translationscmsphrases.view":false,"revisions.view":false,"redirects.view":false,"paymethods.view":false,"deliveriesall.view":false,"deliveries.view":false,"npcities.view":false,"npwarehouse.view":false,"translationsphrases.view":false,"usersgroup.view":false,"users.view":false,"groups.view":false}'),
        ]);
    }
}
