# encoding: utf-8
# -*- mode: ruby -*-
# vi: set ft=ruby :

# All Vagrant configuration is done below. The "2" in Vagrant.configure
# configures the configuration version (we support older styles for
# backwards compatibility). Please don't change it unless you know what
# you're doing.

Vagrant.configure("2") do |config|
  # The most common configuration options are documented and commented below.
  # For a complete reference, please see the online documentation at
  # https://docs.vagrantup.com.

  # Every Vagrant development environment requires a box. You can search for
  # boxes at https://vagrantcloud.com/search.
  config.vm.box = "ggrosz/debian10.lamp"
  config.vm.box_version = "0.2.0"

  # Install the env plugin for Vagrant to use the .env file
  # Command to run in host shell: vagrant plugin install vagrant-env
  # Variable specified in the ./.env file, will be available in the ENV['VARIABLE_NAME']
  # Example:
  # .env: DB_NAME = database
  # Vagrantfile: ENV['DB_NAME']
  config.env.enable

  config.vm.define ENV['PROJECT_NAME']

  # SQL queries to create database and user and grant permissions
  # Values from the .env file are used
  Q1 = "CREATE DATABASE IF NOT EXISTS #{ENV['DB_NAME']}"
  SQL_DB_CREATE = "#{Q1}"

  Q2 = "CREATE USER IF NOT EXISTS '#{ENV['DB_NAME']}'@'localhost' IDENTIFIED BY '#{ENV['DB_PASSWORD']}';"
  Q3 = "GRANT ALL ON #{ENV['DB_NAME']}.* TO '#{ENV['DB_USER']}'@'%' IDENTIFIED BY '#{ENV['DB_PASSWORD']}' WITH GRANT OPTION;"
  Q4 = "FLUSH PRIVILEGES;"
  SQL_DB_USER = "#{Q2}#{Q3}#{Q4}"

  # Setting post vagrant up message
  $msg = <<-MSG
  --------------------------------------------------
  --------------------------------------------------

  Info >>

  Review the .env file in current directory

  SSH into guest machine with: vagrant ssh

  Run 'composer install'

  Run './env-install.sh'

  Site address from host machine:
  http://#{ENV['PROJECT_BASE_URL']}:#{ENV['HTTP_PORT']}

  Phpmyadmin address from host machine:
  http://#{ENV['PROJECT_BASE_URL']}:#{ENV['HTTP_PORT']}/phpmyadmin

  --------------------------------------------------
  --------------------------------------------------
  MSG
  config.vm.post_up_message = $msg

  # Disable automatic box update checking. If you disable this, then
  # boxes will only be checked for updates when the user runs
  # `vagrant box outdated`. This is not recommended.
  # config.vm.box_check_update = false

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine. In the example below,
  # accessing "localhost:8080" will access port 80 on the guest machine.
  # NOTE: This will enable public access to the opened port
  config.vm.network "forwarded_port", guest: 80, host: ENV['HTTP_PORT']

  # Create a forwarded port mapping which allows access to a specific port
  # within the machine from a port on the host machine and only allow access
  # via 127.0.0.1 to disable public access
  # config.vm.network "forwarded_port", guest: 80, host: 8080, host_ip: "127.0.0.1"

  # Set hostname
  config.vm.hostname = ENV['PROJECT_NAME']

  # Create a private network, which allows host-only access to the machine
  # using a specific IP.
  config.vm.network "private_network", ip: "192.168.33.10"

  # Create a public network, which generally matched to bridged network.
  # Bridged networks make the machine appear as another physical device on
  # your network.
  # config.vm.network "public_network"

  # Share an additional folder to the guest VM. The first argument is
  # the path on the host to the actual folder. The second argument is
  # the path on the guest to mount the folder. And the optional third
  # argument is a set of non-required options.
  config.vm.synced_folder "./", "/var/www/vhosts/#{ENV['PROJECT_BASE_URL']}",
    id: "core",
    :nfs => true,
    :mount_options => ['nolock,vers=3,udp,noatime']

  # Setting default directory to chdir into when vagrant ssh is ran
  config.ssh.extra_args = ["-t", "cd /var/www/vhosts/#{ENV['PROJECT_BASE_URL']}; bash --login"]

  # Provider-specific configuration so you can fine-tune various
  # backing providers for Vagrant. These expose provider-specific options.
  # Example for VirtualBox:
  #
  # config.vm.provider "virtualbox" do |vb|
  #   # Display the VirtualBox GUI when booting the machine
  #   vb.gui = true
  #
  #   # Customize the amount of memory on the VM:
  #   vb.memory = "1024"
  # end
  #
  # View the documentation for the provider you are using for more
  # information on available options.

  # Enable provisioning with a shell script. Additional provisioners such as
  # Ansible, Chef, Docker, Puppet and Salt are also available. Please see the
  # documentation for more information about their specific syntax and use.

  config.vm.provision "file", source: "./vagrant/source.vagrant.localhost.conf", destination: "$HOME/install/#{ENV['PROJECT_BASE_URL']}.conf"

  config.vm.provision "shell", inline: <<-SHELL
    echo "----------------------------"
    echo "Base provisioning started..."
    echo "----------------------------"
    wget -q -O install/composer-setup.php https://getcomposer.org/installer
    sudo php install/composer-setup.php --install-dir=/usr/local/bin --#{ENV['COMPOSER_VERSION']} --filename=composer
    mysql -uroot -e \"#{SQL_DB_CREATE}\"
    mysql -uroot -e \"#{SQL_DB_USER}\"
    sed -i 's/PROJECT_BASE_URL/#{ENV['PROJECT_BASE_URL']}/g' ./install/#{ENV['PROJECT_BASE_URL']}.conf
    sudo cp ./install/#{ENV['PROJECT_BASE_URL']}.conf /etc/apache2/sites-available/
    sudo a2ensite #{ENV['PROJECT_BASE_URL']}
    sudo systemctl restart apache2
    sudo rm -rf ./install
  SHELL

  config.vm.provision "shell", inline: <<-UNPRIVILEGED, privileged: false
    composer config -g github-oauth.github.com #{ENV['COMPOSER_AUTH']}
  UNPRIVILEGED

  if ENV['MACHINE_TYPE'] == 'dev'
    config.vm.provision "shell", inline: <<-DEVSHELL
      echo "---------------------------"
      echo "Dev provisioning started..."
      echo "---------------------------"
      sudo apt-get update
      sudo apt-get install php7.4-xdebug
      sudo systemctl restart apache2
    DEVSHELL
  end

  if ENV['MACHINE_TYPE'] == 'test'
    config.vm.provision "shell", inline: <<-TESTSHELL
      echo "----------------------------"
      echo "Test provisioning started..."
      echo "----------------------------"
    TESTSHELL
  end

  if ENV['MACHINE_TYPE'] == 'prod'
    config.vm.provision "shell", inline: <<-PRODSHELL
      echo "----------------------------"
      echo "Prod provisioning started..."
      echo "----------------------------"
    PRODSHELL
  end
end
