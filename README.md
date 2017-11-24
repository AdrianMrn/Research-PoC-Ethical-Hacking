# Research-PoC-Ethical-Hacking
I want to see how easily people are lured into connecting to a random 'free' Wi-Fi hotspot and unknowingly give me their login credentials to various websites.

I'll be using a [Raspberry Pi 3 Model B](https://www.raspberrypi.org/products/raspberry-pi-3-model-b/) to create an unprotected wifi hotspot with a [captive portal](https://en.wikipedia.org/wiki/Captive_portal) and try to make dns requests for popular social media websites resolve to a webserver running on the rpi.

I'll try to use the [Kupiki Hotspot Script](https://github.com/pihomeserver/Kupiki-Hotspot-Script) to get the hotspot running first. It's a script that automatically creates a wifi hotspot that already includes some things like a captive portal and a secure authentication process. I will attempt to rewrite this authentication process to disable the hashing of passwords entered in the captive portal.

Some things that had to be done manually using the Kupiki Hotspot Script:
- sudo apt-get install mariadb-server

Issues during installation: 
- At one point, the NIC is reset, changing the IP address, default gateway etc. The problem is, I was using the wifi NIC to both connect to the internet and host the hotspot. This is obviously not possible, so I had to connect the Pi3 to the internet using an ethernet cable.


# Reading material and sources
https://raspberrypihq.com/how-to-turn-a-raspberry-pi-into-a-wifi-router/
https://github.com/xaneem/wifi-hotspot
https://github.com/pihomeserver/Kupiki-Hotspot-Script
https://freeradius.org/
https://coova.github.io/CoovaChilli/
https://github.com/wifidog
http://sirlagz.net/2012/08/09/how-to-use-the-raspberry-pi-as-a-wireless-access-pointrouter-part-1/
http://sirlagz.net/2013/08/23/how-to-captive-portal-on-the-raspberry-pi/
https://pimylifeup.com/raspberry-pi-captive-portal/
http://www.pihomeserver.fr/en/2014/05/22/raspberry-pi-home-server-creer-hot-spot-wifi-captive-portal/
https://medium.com/@edoardo849/turn-a-raspberrypi-3-into-a-wifi-router-hotspot-41b03500080e
