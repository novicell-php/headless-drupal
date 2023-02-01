# Drupal backend for kino.dk
A Drupal Premium headless project for the new kino.dk backend.

The project was initiated using our [Installation and initial setup](https://novicell.atlassian.net/wiki/spaces/TD/pages/3652255798/Installation+and+initial+setup).

### The full stack is:
- Drupal 9
- PHP 8.0 (with Composer 2)
- Nginx 1.21
- Redis latest
- mariadb latest
- traefik latest
- Node 16 LTS

## Development
You can run this project in a local environment using either Ddev or locally installed with LAMP stack.

## Getting Started
These instructions will get you a copy of the project up and running on your local machine for development and testing
purposes. See deployment for notes on how to deploy the project staging and production systems.

Some environments might have variations of versions of the software listed above. If you are unsure, please contact the project
technical lead. To build the frontend you will need Node 16 or higher installed.

### DDEV
Ddev sets up the project and necessary backing services. We use Node.js to build the frontend, which can be done both
on the host machine or within the `web` docker container created by Ddev.

#### Benefits
Ddev is fast, very reliable, and we have a lot of in house experience using it during development.

#### Requirements
On your host machine install the following:
- [Git](https://git-scm.com/)
- [Docker](https://ddev.readthedocs.io/en/stable/users/docker_installation/)
- [DDEV](https://ddev.readthedocs.io/en/stable/)
- [Node.js](https://nodejs.org/en/) - _**Note: this project require version 16 as set in .ddev/web-build/Dockerfile**_

1. Clone the project repository
   ```sh
   git clone git@bitbucket.org:novicell/kino_drupal.git
   ```
2. Enter the newly created project directory
   ```sh
   cd kino_drupal
   ```
3. Install dependencies with Composer
   ```sh
   composer install
   ```
4. Install the Drupal Premium project sub theme dependencies with NPM:
   ```sh
   cd webroot/sites/kino.dk/themes/custom/kino && npm ci && npm run build:prod
   ```
5. Create local `.env` file from `.env.dist`:
   ```sh
   cp .env.dist .env
   ```
   _**Note: Some of the values depend on your development machine, otherwise the rest can be found in 1password.**_
6. Now run project with DDEV - the database is available internally via sharepoint (ask a fellow developer):
   ```sh
   ddev import-db
   ```
7. Import the latest database using DDEV:
   ```sh
   ddev start
   ```
   Project can be reached at [https://kino_drupal.localhost](https://kino_drupal.localhost)

### DevSpace - Deploy & Develop
DevSpace is an open-source developer tool for Kubernetes that lets you develop and deploy cloud-native software faster.

#### Benefits
There are many benefits to this way of running a development environment. You might offload some load from your pc
letting cloud computing run the containers and making development- and test environment in order to demo new features.

The setup is running off our own docker images and we have added benefit in catching eventual errors upfront.

_**Note: With this approach we are also able to test integrations and features that require access to live API's.**_

#### Requirements
On your host machine install the following:

- [DevSpace CLI](https://devspace.sh/docs/getting-started/installation?x0=5)
- [Loft CLI](https://loft.sh/docs/getting-started/install/cli)
- [Kube Utilities](https://kubernetes.io/docs/tasks/tools/) (kubectl, kubectx, kubens and helm)

_**Note: (On Mac: brew install kubectl kubectx kubens helm)**_

1. Clone the project repository:
   ```sh
   git clone git@bitbucket.org:novicell/kino_drupal.git
   ```
2. Enter the newly created project directory:
   ```sh
   cd kino_drupal
   ```
3. Log in to Loft and select login with Microsoft - using username and password:
   ```sh
   loft login loft.drupal.dk
   ```
4. Create your own Virtual Cluster (VCluster) with Loft CLI - replace `<initials>` with your Novicell initials:
   ```sh
   loft create vcluster <initials>
   ```
5. Create a namespace for your project on DevSpace with `kubectl`:
   ```sh
   kubectl create namespace <your project>-<your initials>
   ```
   _Namespace example: `kinodrupal-cbr`_
6. Choose the new namespace for DevSpace:
   ```sh
   devspace use namespace <your namespace>
   ```
7. Add your database in `.sql` format to the `.novi/dbdumps` directory and proceed.
8. Begin development by deploying project to DevSpace:
   ```sh
   devspace dev -b
   ```
   **_Notes and tips: `-b` or `--force-build` flag is used to rebuild all images (even if they could be skipped because
   context and Dockerfile have not changed)._**
9. You can visit the development project at the domain selected during configuration.
   **_Note: You can always use the `devspace print` command to view the entire DevSpace configuration._**

##### Switch project
If you want to switch to another Novicell project follow this guide while currentl working on the kino_drupal
project:

1. Create namespace with `kubectl` for our other project:
   ```sh
   kubectl create namespace <other project namespace>
   ```
2. Tell DevSpace to use our other projects namespace:
   ```sh
   devspace use namespace <other project namespace>
   ```
3. Now build and deploy your new project:
   ```sh
   devspace dev -b
   ```
Next time we want to develop on Kino Drupal or your other Novicell project go into the folder of the project and
run:
   ```sh
   devspace dev
   ```
   _**Note: If you are not on the correct namespace it will write somthing along these line:
   “HEY, YOU’RE IN THE WRONG NAMESPACE WANNA SWITCH?” and then you select the correct namespace and run
   `devspace dev`**_

   _**Note: If the need arise to delete the `vendor` directory or content, it is important to only delete the contents
   of the directory and not the directory itself.**_

##### DevSpace Terminology
###### Cluster
A cluster in Kubernetes is a collection of servers where your project will be running, in general you do not have to
worry about this since your stuff will run in a VCluster.

###### VCluster
A VCluster (Virtual Cluster) is effectively a Kubernetes cluster, on top of the main Kubernetes cluster - think a
virtual machine.

You can have multiple VClusters for all kinds of projects, be it a CI/CD project, or your web-development projects,
hosting of a site for a customer, etc. But you can only be connected to a single VCluster at a time from your computer.

###### Namespaces
A namespace is effectively a separation of your projects, for example say you are running SiteA and developing on it,
that will be running in a namespace called `sitea`. While SiteB a different project, runs in a namespace called `siteb`.
This lets you separate them from each other, while still running in the overall VCluster - which itself is running
inside the main cluster.

###### Loft
Loft is a tool that sits on the cluster, and allows developers to self-service create vclusters, namespaces, etc. using
the loft cli tool It also has a nice webUI that lets a developer get a visual view of everything running in the cluster,
some other developers vcluster and their own vcluster.

### Traditional development environment
Install Apache, MariaDB (or MySQL), PHP 8 and Composer - [Guide to Ubuntu LAMP stack install](https://www.digitalocean.com/community/tutorials/how-to-install-linux-apache-mysql-php-lamp-stack-on-ubuntu-20-04)

Use a Linux pc/server or setup an alternative way of running the stack on your machine if using Windows or Macintosh PC
like [XAMPP](https://www.apachefriends.org/) or [WAMP](https://www.wampserver.com/en/)

1. Clone the project repository
   ```sh
   git clone git@bitbucket.org:novicell/kino_drupal.git
   ```
2. Enter the newly created project directory
   ```sh
   cd kino_drupal
   ```
3. Install dependencies with Composer
   ```sh
   composer install
   ```
4. Install the Drupal Premium project sub theme dependencies with NPM:
   ```sh
   cd webroot/sites/kino.dk/themes/custom/kino && npm ci && npm run build:prod
   ```
5. Make sure that your Apache server is configured correctly depending on local LAMP-stack.

## Composer
New functionality is often added through contributed, premium or custom modules. These are installed and maintained
using Composer, which is a dependency manager for PHP. This documentation on [Composer](https://getcomposer.org/doc/01-basic-usage.md)
will get you up to speed and managing dependencies in no time.

**_Note: While developing new features or structures we do not wish to update our Composer dependencies unless this is
necessary. This means not running `composer update` if this is not part of the task at hand._**

### Quick commands list
To add a dependency to a project:
   ```sh
   composer require drupal/example_contrib_module
   ```
To install all dependencies from composer.json or composer.lock:
   ```sh
   composer install
   ```
Changes to dependencies are written to `composer.json` and `composer.lock` in the root of our project, from where they
can be committed and pushed remotely.

###  Drupal configuration during development
While developing new feature or structure in Drupal it is important to export the configuration of the feature you are
working on. You can always check the status of the configuration synchronisation using Drush:
   ```sh
   drush config:status
   ```
If you have changes to the configuration use Drush to export these:
   ```sh
   drush cex -y
   ```
The configuration will be written to `.yml` files in `/config/sync`, from where they can be committed and pushed.

_**Note: When exporting config do a check on language switches (they do unfortunately happen) and overwriting customized
config (like standard Drupal mails changed through core/updb - we have that issue on other projects)

## Git strategy & collaboration
We use Git in order to ease collaboration and create versioning of our code. Furthermore, we aim to improve the overall
quality during development by keeping our git strategy lean and without complexity. This in order to get a dynamic
workflow that suites the size of the project and developer team.

### Branch naming convention and commit message
The branch naming convention has been taken from Gitflow and customised with the standard Novicell approach - some
examples and benefits:

* Branch name should include branch type (feature or hotfix) and issue tag (example: `INTRABEI-50`):
   ```
   feature/PROJECTNAME-50
   ```
By including the issue tag in the commit messages and branch name we get the benefit of the Bitbucket/Jira integration.

### Trunk based development & rebase
Trunk-based development is a version control management practice where developers merge small, frequent updates to a
core “trunk” or master branch. Aiming to streamline merging and integration phases, it helps achieve CI/CD and increases
software delivery and team performance.

You can find an article outlining some benefits and best practices of [trunk based development here](https://www.atlassian.com/continuous-delivery/continuous-integration/trunk-based-development).

#### Rebase in developer workflow
Developers work differently but here is an example of a workflow using Git for this project.

Pull the latest changes of the master branch - _**Note: use `--rebase` flag to align our local master’s history
with the remote**_:
   ```sh
   git pull --rebase origin master
   ```
Create a feature branch before starting new work:
   ```sh
   git switch -c my-awesome-feature-branch
   ```
Make some changes and commit often in the feature branch:
   ```sh
   git add .
   ```
   ```sh
   git commit -m "My awesome comment"
   ```
Periodically rebase your work onto master branch - in case new features have been merged to master.
First off we update our local master branch:
   ```sh
   git checkout master
   ```
   ```sh
   git pull --rebase origin master
   ```
Include the latest commits of our local master branch, and get them into our local feature branch.
   ```sh
   git checkout feature/my-awesome-feature-branch
   ```
   ```sh
   git rebase master
   ```
_**Note: During a rebase you might have to deal with conflicts, this is expected and unavoidable if there are changes**_:

Now our branch is up-to-date. Now we build and test locally again after which we can push to our remote:
   ```sh
   git push origin feature/my-awesome-feature-branch --force
   ```
_**Note: new commits are added to the branch and by using the `--force` or  `-f` flag we allow git to overwrite the
history of the remote branch. Forgetting this step will lead to a Git error as the branch histories of local and remote
differ.**_

Lastly the developer merges the new feature to staging branch. For added benefits a Pull Request (PR) can be created as
the foundation for a code review or a colleague giving their input. Once the code has been tested on staging and/or
reviewed it is merged to our master branch. This strategy gives less importance to the maintenance and state of the
staging branch, which can always be recreated when needed - all code is always merged to master after test and/or code
review.
_**Note: Any existing PR will be updated with any changes made to our feature branch.**_

### Pull requests & reviews
It is good practice that new features are submitted to the project in the form of Pull Requests, which are subsequently
reviewed and approved by other developers on the team. A flow for good handover should include the following before
reassign:

* Locally building project with new feature.
* Testing new feature locally.
* Create PR including an added/removed lists.
* Short guide as to how the feature or change can be tested by reviewer.
* Communicate if the PR is blocked by another PR, decision or action - for complex integrations include check list.

#### Best practices for best collaboration
Git and collaboration on a big team can be confusing at times, and we have gathered a few best practices to ensure good
collaboration:

* Only fast-forward merges to trunk.
* Merges come from Pull Requests (PR), which invites team members to engage - master branch is protected.
* Merge squash your multiple changes in short live branches - improved git history.
* Rebasing master against your short-lived branch to keep it up to date is best.
_**Note: do NOT merge master back to your short-lived branch and then back again to trunk, effectively creating multiple
merge commits and confusing history.**_
* Feature branches should be short-lived - this fits our delivery model and tasks broken down to maximum two working
days.
* Keep commit messages as concise as possible, while still making sense and add the case number as the first thing.
* NEVER commit secrets of any kind to the repository — EVER.
* With Composer use the `--sort-packages` flag, which is a nice feature, though it should be a default on composer 2,
it is not always running automatically ¯\_(ツ)_/¯
* Let us keep the repository clean and delete old feature branches.

## Deployment
### Staging
### Production
