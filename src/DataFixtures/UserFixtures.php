<?php

namespace App\DataFixtures;

use App\Entity\User;
use App\Entity\Shoe;
use App\Entity\Category;
use App\Entity\Order;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Create categories
        $categories = [];
        $categoryNames = ['Sneakers', 'Boots', 'Sandals', 'Heels'];
        foreach ($categoryNames as $name) {
            $category = new Category();
            $category->setName($name);
            $category->setDescription("Description for $name");
            $manager->persist($category);
            $categories[] = $category;
        }

        // Create shoes
        $shoes = [];
        $shoeData = [
            ['name' => 'Running Shoe', 'brand' => 'Nike', 'price' => 120.00, 'sizes' => ['8', '9', '10'], 'colors' => ['Black', 'White'], 'category' => $categories[0]],
            ['name' => 'Winter Boot', 'brand' => 'Timberland', 'price' => 200.00, 'sizes' => ['7', '8', '9'], 'colors' => ['Brown', 'Black'], 'category' => $categories[1]],
            ['name' => 'Beach Sandal', 'brand' => 'Birkenstock', 'price' => 80.00, 'sizes' => ['6', '7', '8'], 'colors' => ['Blue', 'Green'], 'category' => $categories[2]],
            ['name' => 'High Heel', 'brand' => 'Jimmy Choo', 'price' => 500.00, 'sizes' => ['5', '6', '7'], 'colors' => ['Red', 'Black'], 'category' => $categories[3]],
        ];
        foreach ($shoeData as $data) {
            $shoe = new Shoe();
            $shoe->setName($data['name']);
            $shoe->setBrand($data['brand']);
            $shoe->setPrice($data['price']);
            $shoe->setSizes($data['sizes']);
            $shoe->setColors($data['colors']);
            $shoe->setCategory($data['category']->getName());
            $shoe->setDescription("Description for {$data['name']}");
            $manager->persist($shoe);
            $shoes[] = $shoe;
        }

        // Create admin user
        $admin = new User();
        $admin->setUsername('admin');
        $admin->setRoles(['ROLE_ADMIN']);
        $hashedPassword = $this->passwordHasher->hashPassword($admin, 'admin123');
        $admin->setPassword($hashedPassword);

        $manager->persist($admin);

        // Create regular user
        $user = new User();
        $user->setUsername('user');
        $user->setRoles(['ROLE_USER']);
        $hashedPassword = $this->passwordHasher->hashPassword($user, 'user123');
        $user->setPassword($hashedPassword);

        $manager->persist($user);

        // Create orders
        $orderData = [
            ['customerName' => 'John Doe', 'customerPhone' => '09123456789', 'customerAddress' => '123 Main St', 'paymentMethod' => 'Credit Card', 'selectedColor' => 'Black', 'selectedSize' => '9', 'status' => 'Pending'],
            ['customerName' => 'Jane Smith', 'customerPhone' => '09987654321', 'customerAddress' => '456 Elm St', 'paymentMethod' => 'PayPal', 'selectedColor' => 'White', 'selectedSize' => '8', 'status' => 'Completed'],
        ];
        foreach ($orderData as $data) {
            $order = new Order();
            $order->setCustomerName($data['customerName']);
            $order->setCustomerPhone($data['customerPhone']);
            $order->setCustomerAddress($data['customerAddress']);
            $order->setPaymentMethod($data['paymentMethod']);
            $order->setSelectedColor($data['selectedColor']);
            $order->setSelectedSize($data['selectedSize']);
            $order->setStatus($data['status']);
            $order->setOrderDate(new \DateTimeImmutable());
            $randomShoe = $shoes[array_rand($shoes)];
            $order->setShoe($randomShoe);
            $order->setTotalAmount($randomShoe->getPrice());
            $manager->persist($order);
        }

        $manager->flush();
    }
}