<?php

namespace App\Command;

use App\Carte;
use App\Pioche;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Couleur;
use App\Valeur;
use Symfony\Component\Console\Question\ChoiceQuestion;

#[AsCommand(
    name: 'jeu',
    description: 'Jeu de carte',
)]
class JeuCommand extends Command
{
    protected function configure(): void
    {
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);

        $ordreCouleurs = Couleur::cases();
        shuffle($ordreCouleurs);

        $io->info("L'ordre des couleurs est le suivant : {$this->printEnum($ordreCouleurs)}");

        $ordreValeurs = Valeur::cases();
        shuffle($ordreValeurs);

        $io->info("L'ordre des valeurs est le suivant : {$this->printEnum($ordreValeurs)}");

        $helper = $this->getHelper('question');
        $question = new ChoiceQuestion('Veuillez choisir un nombre de cartes à tirer (1 - 52) : ', range(1, 52));
        $nbCartes = $helper->ask($input, $output, $question);
        $io->info("Vous avez choisi de tirer $nbCartes cartes.");

        $pioche = new Pioche();
        $main = $pioche->piocher($nbCartes);

        $io->info("Voici votre main non triée : ");
        foreach ($main as $carte) {
            $io->writeln($this->printCarte($carte));
        }

        $tri = $pioche->tri($main, $ordreCouleurs, $ordreValeurs);
        $io->info("Voici votre main triée : ");
        foreach ($tri as $carte) {
            $io->writeln($this->printCarte($carte));
        }

        $io->success('Fin.');

        return Command::SUCCESS;
    }

    private function printEnum(array $array): string
    {
        return array_reduce($array, fn ($prev, $curr) => "$prev $curr->value", '');
    }

    private function printCarte(Carte $carte): string
    {
        return "{$carte->valeur->value} de {$carte->couleur->value}";
    }
}
