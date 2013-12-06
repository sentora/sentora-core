Vagrant.configure("2") do |config|
## zpanel dev environments
# ubuntu 12.04
	config.vm.define "zp_dev_ubuntu_12_04" do |config|
		config.vm.box = "ubuntu12_04-32"
		config.vm.network :private_network, ip: "192.168.25.10"
		config.vm.box_url = "http://cloud-images.ubuntu.com/vagrant/precise/current/precise-server-cloudimg-i386-vagrant-disk1.box"
		## mount folders
		# configs
		config.vm.synced_folder "./etc/build/config_packs/ubuntu_12_04/", "/etc/zpanel/configs/",
        		:owner =>"root", :group => "root", :mount_options => ['dmode=777,fmode=777']
		# docs , not really needed but lets load it any way
		config.vm.synced_folder "./doc/", "/etc/zpanel/docs/",
        		:owner =>"root", :group => "root", :mount_options => ['dmode=777,fmode=777']
		# , panel
		config.vm.synced_folder "./", "/etc/zpanel/panel/",
        		:owner =>"root", :group => "root", :mount_options => ['dmode=777,fmode=777']

		config.vm.synced_folder "./etc/apps/", "/etc/zpanel/etc/apps/",
        		:owner =>"root", :group => "root", :mount_options => ['dmode=775,fmode=775']

		config.vm.synced_folder "./etc/apps/", "/etc/zpanel/panel/etc/apps/",
        		:owner =>"root", :group => "root", :mount_options => ['dmode=775,fmode=644']
# chmod 644 /etc/zpanel/panel/etc/apps/phpmyadmin/config.inc.php

		config.vm.provision "shell", path: "./vagrant_provision.sh"
	end # zp_dev_ubuntu_12_04

end # top level end
