<?php

namespace App\Command;

use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ReadfilecsvCommand extends Command
{
    private string $csvDirectory;
    
    protected static $defaultName = 'ReadFileCsv';
    protected static $defaultDescription = 'Add a short description for your command';

    
    public function ___construct(string $csvDirectory)
    {
        parent::__construct(self::$defaultName);
        $this->csvDirectory = $csvDirectory;
    }

    
    protected function configure(): void
    {
        $this->setDescription("Afficher les données d'un fichier CSV")
            ->addArgument('fichier', InputArgument::REQUIRED, 'le nom du fichier.')
            ->addArgument('Json', InputArgument::OPTIONAL, 'Afficher en format Json')
            ->setHelp("La commande pour lire le fichier : ReadFileCsv [Argument: nom du fichier.extension] [option: Json]")
        ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $output->writeln([
            'Le fichier <fg=green>'.$input->getArgument('fichier').'</> contient :',
            '=========================================================',
            '',
        ]);
        
        if($input->getArgument('Json'))
        {
            $output->writeln($this->csvToJson($output,$input));
        }else {
            $this->readCsv($output,$input);
        }

        $output->writeln([
            '',
            '=========================================================',
            '',
        ]);
         
    }

    /**
     * Permet de lire un fichier csv et d'afficher les donnée dans un tableau
     *
     * @param [type] $output
     * @return void
     */
    private function readCsv($output, $input): void
    {
        $io = new SymfonyStyle($input, $output);
        // Récupération des datas
        $data = $this->saveData($output, $input);
        // Creation du tableau 
        $table = new Table($output);
        $table->setHeaders(['Sku', 'Status', 'Price', 'Description', 'Created At', 'Slug'])
            ->setRows($data);
        // Rendu du tableau
        $table->render();
        // Message de succés
        $io->success('Le fichier est correct...');   
    }

    /**
     * Permet la création en format Json
     *
     * @param [type] $output
     * @param [type] $input
     * @return void
     */
    private function csvToJson($output,$input)
    {
        $io = new SymfonyStyle($input, $output);
        // Récupération des données
        $data = $this->saveData($output, $input);
        // Message de succés 
        $io->success('Le fichier est correct...');
        // Retourne le Json
        return json_encode($data);
            
    }

    /**
     * Permet de recuperer les données du fichier csv
     *
     */
    private function saveData($output, $input)
    {
        // Extension Autoriser
        $extensionValid = ['csv'];
        // Chemin du fichier
        $pathfile = dirname(dirname(__DIR__))."\csv\\".$input->getArgument('fichier');
        // Récupération de l'extension
        $extension = explode(".", $input->getArgument('fichier'));
        // Vérification de l'extension
        if(in_array($extension[1], $extensionValid))
        {
            // Vérification si le fichier existe dans le dossier
            if(file_exists($pathfile)){
                // Ouverture et lecture du fichier .csv
                $delim = ';';
                $csvFile = file($pathfile); // lire le fichier
                $firstline = str_getcsv($csvFile[0], $delim); // recuperation du header

                // récupération des données
                foreach ($csvFile as $line) {
                    $line   = str_getcsv($line, $delim);
                    // Gestion du status
                    if($line[2] == "is_enable")
                    {
                        $line[2] = "status";
                    }elseif($line[2] == "0")
                    {
                        $line[2] = "Disable";
                    }else{
                        $line[2] = "Enable";
                    }
                    // Gestion du price
                    $price = str_replace(".", ",", round(floatval($line[3]),1,PHP_ROUND_HALF_UP)."0 ").$line[4]; // Sauvegarde de l'euro
                    // Gestion du format date
                    $date = date('l, d-M-Y H:i:s e', strtotime($line[6]));
                    // Gestion du slug
                    $slug = str_replace(" ", "-", $line[1]);
                    // Récupération dans un tableau
                    $lines = [$line[0], $line[2], $price, $line[5], $date, $slug];
                    $dataTab[] = $lines;
                    $dataForJson[] = [
                        "Slug" => $line[0],
                        "Status" => $line[2],
                        "Price" => $price,
                        "Description" => $line[5],
                        "Created At" => $date,
                        "Slug" => $slug
                    ];
                }
                unset($dataTab[0]);
                unset($dataForJson[0]);

                if($input->getArgument("Json"))
                {
                    return json_encode($dataForJson);
                } else {
                    return $dataTab;
                }
            
            } else {
                // message d'erreur si le fichier n'existe pas.
                return $output->writeln("<error>Le fichier n'existe pas dans le dossier.</error>");
            }
        } 
        else {
            // message d'erreur si l'extension n'existe pas'.
            return $output->writeln("<error>L'extension du fichier n'est pas valide seul le csv est autorisé.</error>");
        }        
    }
}
