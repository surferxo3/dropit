## Synopsis

This project aims to demonstrate the publisher and consumer architecture in RabbitMQ. The consumer script is able to fetch single or multiple messages (in loop) from queue.

## Installation

Running the script is just the matter of minutes and not ~~seconds~~. Here are the steps listed (in order):
* Download VMware / VirtualBox and [Ubuntu Server] (https://virtualboxes.org/images/ubuntu-server/) image. After that power-up your VM.
* For RabbitMQ installation
  * If you are comfortable with Docker than follow [these] (https://hub.docker.com/_/rabbitmq/) steps.
  * Else for manual installation (that I found most useful) execute the commands below in your server:
```
#step 1 - rabbitmq-server installation with management plugin
echo "deb http://www.rabbitmq.com/debian/ testing main"  | sudo tee  /etc/apt/sources.list.d/rabbitmq.list > /dev/null
sudo wget http://www.rabbitmq.com/rabbitmq-signing-key-public.asc // to add the verification key for the package
sudo apt-key add rabbitmq-signing-key-public.asc
sudo apt-get update
sudo apt-get install rabbitmq-server --force-yes // you can skip --force-yes
sudo service rabbitmq-server start // optional as rabbitmq-server starts automatically
sudo rabbitmq-plugins enable rabbitmq_management
sudo service rabbitmq-server restart

#step 2 - create new user as latest rabbitmq release doesn't allow guest account to be accessed from other than localhost
rabbitmqctl add_user msharaf msharaf
rabbitmqctl set_user_tags msharaf administrator
rabbitmqctl set_permissions -p / msharaf ".*" ".*" ".*" // you can skip this and set permission later from rabbitmq web ui

#step 3 - note your inet addr
ifconfig -a
```

* Now change the *config.php* according to your installation scenario.
* Execute `php composer.phar install` in your project root to download the dependencies.
* Finally, run *sender.php* to push message to queue and *receiver.php* to consume message(s) from queue.

## Note
* For configuring network in your VM kindly follow the steps below:
  * If using VMware than first remove your network adapter and add it again with **Network Connection: Bridged**.
  * If using VirtualBox than select **Bridged Connection** and not **NAT**.
* On your local machine the rabbitmq-server is accessed via port 5672 and its web ui is accessed via port 15672. E.g:
  * For running your script goto: [http://vm-server-ip:5672/sender.php] (http://vm-server-ip:5672/sender.php).
  * For accessing management panel goto: [http://vm-server-ip:15672] (http://vm-server-ip:15672).

This demo was built with love during learning of AMQP. For more info about [ME] (http://bit.ly/msharaf-linkedin) visit my profile.
