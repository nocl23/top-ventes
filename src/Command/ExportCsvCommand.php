<?php

namespace App\Command;

use App\Entity\Category;
use App\Entity\Orders;
use App\Entity\Product;
use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ExportCsvCommand extends Command
{
    protected static $defaultName = 'export-top-ventes';

    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * ExportCsvCommand constructor.
     * @param null $name
     * @param ContainerInterface $container
     * @param EntityManager $em
     */
    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function configure()
    {
        $this
            ->setDescription('Add a short description for your command')
            ->addArgument('arg1', InputArgument::OPTIONAL, 'Argument description')
            ->addOption('option1', null, InputOption::VALUE_NONE, 'Option description')
        ;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @return int|void|null
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $productRepository  = $this->container->get('doctrine')->getRepository(Product::class);
        $categoryRepository = $this->container->get('doctrine')->getRepository(Category::class);
        $products           = $productRepository->findTop3();

        if ($products) {
            $categoryList = array();

            foreach ($products as $k => $v) {
                $product = $productRepository->find((int)$v['id']);
                $categoryIdOfProduct = $product->getCategory()->getId();

                if (array_key_exists($categoryIdOfProduct, $categoryList)) {
                    if (count($categoryList[$categoryIdOfProduct]) < 3) {
                        $categoryList[$categoryIdOfProduct][] = array(
                            'product'   => $product,
                            'quantity'  => $v['qty'],
                            'amount'    => $v['price']
                        );
                    }
                } else {
                    $categoryList[$categoryIdOfProduct][] = array(
                        'product'   => $product,
                        'quantity'  => $v['qty'],
                        'amount'    => $v['price']
                    );
                }
            }

            ksort($categoryList, SORT_REGULAR);

            $filename = __DIR__ . '/../../public/top_trois_ventes.csv';
            $file = fopen($filename, 'w+');
            fputcsv(
                $file,
                array(
                    'position',
                    'categories',
                    'product',
                    'quantity',
                    'amount'
                ),
                ';'
            );

            foreach ($categoryList as $key => $category) {
                $i = 0;
                foreach ($category as $k => $v) {
                    fputcsv(
                        $file,
                        array(
                            ++$i,
                            $categoryRepository->find((int) $key)->getName(),
                            $v['product']->getName(),
                            $v['quantity'],
                            $v['amount']
                        ),
                        ';'
                    );
                }
            }

            fclose($file);
            $io->success('L\'exportation des Top Ventes est disponible à "' . $filename . '"');

        } else {
            $io->error('Une erreur s\'est produite lors de l\'exportation des Top Ventes');
        }
    }
}
