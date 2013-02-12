# Sample Project for SCRUM Frontend Web Developer test

## Instructions:

1. Read through the provided documentation describing the feature request
2. Fork the project to your own GitHub account and clone to your local machine
3. Setup the project on your own environment OR use the provided VM using [vagrant](http://www.vagrantup.com/) (second option preferred option)
4. Make the required changes and commit
5. Open a pull request to https://github.com/kodify/frontend-test

## Environment

You have two options in regards to environments: You can use your own development environment or you can use our virtual machine

### ( OPTION A ) Kodify environment using vagrant

* To setup using provided VM you'll need to install the latest versions of the following software for your OS
    * Vagrant: http://downloads.vagrantup.com/
    * Virtual Box: https://www.virtualbox.org/wiki/Downloads
<br>  
* Once you have vagrant installed, fork the project to your GitHub account and clone from there to your machine.

Using the terminal navigate to the directory where you cloned the project and type:

    vagrant up

This process can take anywhere between 10-30 mins depending on your internet connection (it will need to download a VM of around 445 MB)

To login to this new VM, if your host machine is running OSX/Linux/Unix , you can type:

    vagrant ssh

If your host machine is under windows, you have to install any ssh client, such as putty, and login to 127.0.0.1 , port 2222, with the next credentials:

    user: vagrant
    pass: vagrant
    
    
### ( OPTION B ) Using your own environment

your system needs to be compliant with symfony2 requirements: http://symfony.com/doc/2.0/reference/requirements.html

* PHP version > 5.3.2
* Sqlite3 needs to be enabled
* JSON needs to be enabled
* ctype needs to be enabled
* Your PHP.ini needs to have the date.timezone setting


## Setup


go to project docroot, if you are using our vagrant machine the path should be:

    cd /var/www/katt/current/

Install Vendors

     php composer.phar install

You may be asked to enter your github credentials:

```
Cloning failed using an ssh key for authentication, enter your GitHub credentials to access private repos
The credentials will be swapped for an OAuth token stored in /home/vagrant/.composer/config.json, your password will not be stored
To revoke access to this token you can visit https://github.com/settings/applications
Username: *github-username*
Password:
```


Create database and schema

    php app/console doctrine:database:create
    php app/console doctrine:schema:create






## Access to the project

you need to add the next entry in your hosts file

    127.0.0.1       kodify.vagrant

and now you can access using url

    https://kodify.vagrant:30443/

You should see:

![login](https://www.evernote.com/shard/s22/sh/dfd96d45-5272-4794-a6e5-c50413ddd0c4/5b0c4c122bb4674fb511ac07ac9c3e2a/deep/0/https://kodify.vagrant:30443/login.jpg)

Credentials for login:
*username: admin
*password: adminpass

# GOOD LUCK!

