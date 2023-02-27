<?php

use Doctrine\ORM\EntityManager;
use Model\Entity\Admin;

include dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'public' . DIRECTORY_SEPARATOR . 'index.php';

if ($app) {
    $container = $app->getContainer();
    $manager = $container->get(EntityManager::class);
    $admin = new Admin();
    $admin->setName('Admin')
        ->setMail('test@test.fr')
        ->setPhone('0681208051')
        ->setPassword(password_hash('admin', PASSWORD_BCRYPT));

    $manager->persist($admin);
    $manager->flush();

    echo "Admin Saved";
}
