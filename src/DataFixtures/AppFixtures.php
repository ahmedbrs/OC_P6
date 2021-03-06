<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use App\Entity\Category;
use App\Entity\Comment;
use App\Entity\Trick;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AppFixtures extends Fixture
{
    private $encoder;
    private $tricks = [
        "seatbelt" => "La main avant atteint le corps et saisit la queue tandis que la jambe avant est désossée. Le bras du snowboardeur ressemble à la ceinture d'une ceinture de sécurité à trois points , d'où son nom.",
        "slob" => "La main avant attrape muet, la jambe arrière est désossée et la planche est maintenue parallèle au sol.",
        "stiffy" => "Attrapez l'indy entre les fixations et osez les deux jambes à 90 ° par rapport au corps.",
        "stalefish" => "La main arrière saisit le bord du talon de la planche entre les pieds, autour de l'extérieur du genou.",
        "squirrel" => "Une astuce dans laquelle la main avant du cycliste saisit le bord du talon devant le pied avant et sa main arrière / arrière saisit le bord du talon derrière le pied arrière.",
        "swiss-cheese-air" => "La main arrière atteint entre les jambes et saisit le bord du talon devant le pied avant tandis que la jambe arrière est désossée.",
        "tailfish" => "Similaire dans la convention de dénomination d'un Tindy, Tailfish est un portemanteau de «Tail» et «Stalefish». La main arrière saisit le bord du talon entre la fixation arrière et la queue.",
        "tail-grab" => "La main arrière attrape la queue du snowboard. Les variations incluent le redressement ou le «désossage» de la jambe avant, ou le «peaufinage» de la planche légèrement à l'avant ou à l'arrière.",
        "taipan-air" => "La main arrière saisit le bord des orteils juste devant le pied arrière. Cependant, le bras doit faire le tour de l'extérieur de votre genou arrière. La planche est ensuite tirée derrière le cavalier (peaufinée). Le nom Taipan est un portemanteau de la queue / air du Japon.",
        "tindy" => "La prise de tindy est une prise controversée, et le nom est un portemanteau de «queue» et «indy». La main de fuite saisit entre la fixation arrière et la queue sur le bord des orteils."
    ];
    private $categories = [
        'Stances',
        'Straight air',
        'Grabs',
        'Spins',
        'Flips'
    ];
    private $lorem = " Lorem, ipsum dolor sit amet consectetur adipisicing elit. Esse explicabo quaerat doloribus molestias vero atque nesciunt omnis, saepe voluptatem! Animi doloribus iure maiores debitis odio incidunt est a quidem accusamus.";

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $password = $this->encoder->encodePassword($user, 'admin123');
        $user->setUsername('admin')
             ->setPassword($password)
             ->setEmail('ab.perso@outlook.fr')
             ->setAvatar('uploads/avatar-default.png');
        $manager->persist($user);
        
        $tricksName = array_keys($this->tricks);

        for ($i = 0; $i < 5; $i++) { 
            $category = new Category();
            $category->setName($this->categories[$i]);
            $manager->persist($category);

            if ($i === 2) {
                for ($j = 0; $j < 10; $j++) {
                    $trick = new Trick();
                    $trick->setCategory($category)
                          ->setUser($user)
                          ->setTitle(strtolower($tricksName[$j]))
                          ->setDefaultImage('images/default-image.jpg')
                          ->setcontent($this->tricks[$tricksName[$j]] . $this->lorem)
                          ->setCreatedAt(new \DateTime())
                          ->setUpdatedAt(new \DateTime());
                    $manager->persist($trick);
        
                    $comment = new Comment();
                    $comment->setComment($this->lorem)
                            ->setCreatedAt(new \DateTime())
                            ->setUser($user)
                            ->setTrick($trick);
                    $manager->persist($comment);
                }
            }
        }

        $manager->flush();
    }
}
