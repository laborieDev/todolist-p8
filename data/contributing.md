# CONTRIBUTING - P8

> TodoList - Projet 8 OpenClassrooms / Formation PHP Symfony

[![Codacy Badge](https://app.codacy.com/project/badge/Grade/7620a59c877c43d9a1dd692f52e56f53)](https://www.codacy.com/gh/laborieDev/todolist-p8/dashboard?utm_source=github.com&amp;utm_medium=referral&amp;utm_content=laborieDev/todolist-p8&amp;utm_campaign=Badge_Grade)

Lien vers le Repository : https://github.com/laborieDev/todolist-p8

## Environnement utilisé durant le développement
* Symfony 4.2
* Composer 2.0.13
* Mamp 5.5
  * Apache 2.2.34
  * PHP 7.4.12
  * MySQL 5.7.26


## Installer le projet
Suivre ce qui est indiqué dans le [README](<https://github.com/laborieDev/todolist-p8/blob/main/README.md>).

## Les bonnes pratiques
### 1 - Git
Pour toutes nouvelles contributions ou features, merci de respecter les règles suivantes :
* Créez des issues correctement commentées.
* Créez une nouvelle branche à partir de la branche  **main**.
  <br> *Portez attention au nom de la branche.*
* Commentez correctement tout commit.
* Pour toutes features terminées, veuillez créer une pull request.
* Seulement après la validation de votre pull request de *@laborieDev* (créateur du projet), vous pourrez merger votre branche dans **main**.

### 2 - Votre code
* Respectez au minimum des [PSR-1](<https://www.php-fig.org/psr/psr-1/>), [PSR-2](<https://www.php-fig.org/psr/psr-2/>) et [PSR-12](<https://www.php-fig.org/psr/psr-12/>)
* Respectez des [Conventions de Symfony](<https://symfony.com/doc/4.4/contributing/code/conventions.html>)
* Respectez des [Standards de code de Symfony](<https://symfony.com/doc/current/contributing/code/standards.html>)

### 3 - Les bundles
Si le bundle que vous souhaitez installer n'a pas été réalisé par vous même, il doit obligatoirement être installé via COMPOSER.
<br>
Sinon, vous pourrez l'insérer dans le dossier *bundles*.<br>
Si ce dossier n'existe pas encore, n'hésitez pas à le créer.

### 4 - Architecture de fichier
* Vous devrez respecter l'architecture de Symfony 4 pour les fichiers PHP
* Les templates devront se trouver dans un dossier disposé dans *templates* et correspondant au Controller associé
* Les fichiers JS et CSS se trouverons dans le dossier *public*

### 5 - Modification du model de données
Si vous modifiez le modèle de données (ajout d'un champ ou création d'une entité par exemple), vous devriez :
* vérifier que toutes les migrations s'ajoutent bien dans le commit
* modifier le diagramme de classes se trouvant dans *data/uml*

Si les diagrammes de séquence et de cas d'utilisation sont affectés, vous devriez aussi les modifier.

### 6 - Tests unitaires et fonctionnels
PhpUnit est installé sur ce projet pour permettre la crétaion de tests.<br>
Toutes nouvelles features doivent être testées et le taux de couverture global doit toujours être au dessus de 70%.

Un dossier a été créé pour le coverage dans *tests*.

Pour lancer le test avec le coverage :
```
   vendor/bin/phpunit --coverage-html=tests/coverage/report.html
```