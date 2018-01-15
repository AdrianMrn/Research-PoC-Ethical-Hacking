# Research-PoC-Ethical-Hacking

# Kupiki Hotspot Script
I want to see how easily people are lured into connecting to a random 'free' Wi-Fi hotspot and unknowingly give me their login credentials to various websites.

I'll be using a [Raspberry Pi 3 Model B](https://www.raspberrypi.org/products/raspberry-pi-3-model-b/) to create an unprotected wifi hotspot with a [captive portal](https://en.wikipedia.org/wiki/Captive_portal) and try to make dns requests for popular social media websites resolve to a webserver running on the rpi.

I'll try to use the [Kupiki Hotspot Script](https://github.com/pihomeserver/Kupiki-Hotspot-Script) to get the hotspot running first. It's a script that automatically creates a wifi hotspot that already includes some things like a captive portal and a secure authentication process. I will attempt to rewrite this authentication process to disable the hashing of passwords entered in the captive portal.

Some things that had to be done manually using the Kupiki Hotspot Script:
- `sudo apt-get install mariadb-server`
- Rewrite part of the script to install dependencies before restarting the NIC.

Issues during installation: 
- At one point, the NIC is reset, changing the IP address, default gateway etc. The problem is, I was using the wifi NIC to both connect to the internet and host the hotspot. This is obviously not possible, so I had to connect the Pi3 to the internet using an ethernet cable.

Todo:
- Make Raspberry Pi portable (powerbank with 2Amps)
- Create some dummy login webpages & Edit DNS settings to point common domains like facebook.com, ... to my fake webpages (OR just use coovachilli & disable encryption?)
- Have admin panel on hotspot where I can download a file with all the credentials

# Starting Over
I'm starting over at this point because I've figured out the Kupiki hotspot won't serve my needs. My current plan of action is this:
- Use hostapd to turn my Pi3 into a wireless access point with a juicy name (eg _freeWi-Fi)
- Use Dnsmasq to forge DNS entries for websites like facebook.com, paypal.com and others
- Use nginx to serve the fake login forms

I installed everything I needed by using

    sudo apt-get install dnsmasq hostapd nginx

Then I configured my rpi to have a static IP address by going to `/etc/dhcpcd.conf` and adding these lines to the end of the file:

    interface wlan0
    static ip_address=192.168.4.1/24

and restarting the dhcpcd service: `sudo service dhcpcd restart`

Then I added these lines to the end of my `/etc/dnsmasq.conf` file:

    log-facility=/var/log/dnsmasq.log
    interface=wlan0
    dhcp-range=192.168.4.2,192.168.4.200,255.255.255.0,12h
    no-resolv
    log-queries
    address=/#/192.168.4.1

This sets dnsmasq up to deliver IP addresses through DHCP in the 192.168.4.2 - 192.168.4.200 range (about 200 addresses), with a lease time of 12 hours. I'm also logging all queries going through dnsmasq to the `/var/log/dnsmasq.log` file, this can be used to monitor who and/or what is accessing your AP. The last line will set up dnsmasq to resolve all dns queries to the local IP address, where nginx will be hosting our fake captive portal.

Then I edited the hostapd config file `/etc/hostapd/hostapd.conf` to act as an access point:

    interface=wlan0
    driver=nl80211
    ssid=FreeWiFi
    channel=7

This sets the name of the AP to "FreeWiFi", this is what will show up when somebody is scanning for open Wi-Fi networks. I might even add an underscore to the start of the SSID, so it shows up at the top of the list on iOS & osx devices, but this didn't seem to work initially.

To make sure hostapd knows where to find this config file, the `DAEMON_CONF` variable in `/etc/default/hostapd` needs to be set to the config file we just edited(`/etc/hostapd/hostapd.conf`).

Finally, stop and start all services to refresh their settings (simply using `sudo service xxx restart` wouldn't work):

    sudo systemctl stop hostapd
    sudo systemctl stop dnsmasq
    sudo systemctl stop nginx
    sudo service hostapd start
    sudo service dnsmasq start
    sudo service nginx start

Now, if you use your phone or laptop to search for Wi-Fi networks, there should be a network called FreeWiFi. If connected to it and you type in any website's address, it should show the nginx welcome page. On certain devices, you will even get a notification asking you to log in to gain access to the internet, how convenient and helpful of them!

# Captive portal
When I first connected to the AP, I realised the experience was slightly different from other wi-fi hotspots: I didn't get a prompt to log in to get access to the internet. I'll try to figure out how to automatically prompt the user to log in to a so-called captive portal, in the hope that most users will enter their actual credentials, so I can harvest them (ethically, of course).

I joined a local ISP's hotspot and copied their captive portal (html, css, images, js), I will be copying the SSID of their wifi hotspot as well. I edited the login form slightly, to POST to a php script that will save the login credentials to a file in csv format and redirects the user back to the login page afterwards.

At this point I realised Raspbian doesn't come with php installed. Make sure you have php7.0-fpm installed by running `apt-get install php7.0-fpm`.

Make sure to set the permissions to 777 for the file you're writing to, otherwise you might lose some time figuring out why nothing is happening when running the script.

Finally, I created a seperate php script getdata.php that serves the csv file for download. I could password protect this, but I don't think the average user would stumble upon the script.

# Making it seem more credible
I need to edit my nginx settings to best imitate the ISP's captive portal and make it seem as credible as possible.

First, I'm creating a new website in nginx's `sites-available` and creating a subfolder structure that copies the ISP's captive portal. Next, I'm configuring my landing page to redirect the user to this page.

Finally, I don't want the user to get any warnings about the page not running on SSL and their credentials being stolen, because that's not what's happening here at all, so I'll be attempting to get the page to be served over HTTPS.

After installing an SSL certificate for the webserver and accessing the page, I was issued a [huge warning](https://i.ytimg.com/vi/Wn2IBrtEgRg/hqdefault.jpg) telling me my connection was not private. I won't be using a self signed SSL certificate because of this reason. I obviously don't have access to the ISP's domain, so I also can't generate an SSL certificate using a Certificate Authority like Letsencrypt that would work with my server.

# Portability
My Raspberry Pi3 wouldn't start up when connected to my powerbank supplying 1 Amps of power, I looked around for a bit and figured out that a Pi3 needs a pretty beefy powerbank supplying at least 2.1 Amps of power.


# Summary of tools used
1. **Kupiki-Hotspot**

    Major waste of time if you're up to no good.
    Very powerful when setting up a legit hotspot with a Raspberry Pi (includes a customizable captive portal powered by coovachilli, a secure authentication process based on freeRadius and a built-in management interface with hotspot options, vouchers, ...).

2. **hostapd**

    Tool to turn your linux machine into a hotspot (actually the same tools that turns your Android device into a hotspot).

3. **nginx**

    High performance web server with a relatively easy setup process.

4. **dnsmasq**

    Very lightweight tool that provides DNS, DHCP and some other services.


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

https://blog.heckel.xyz/2013/07/18/how-to-dns-spoofing-with-a-simple-dns-server-using-dnsmasq/

https://andrewmichaelsmith.com/2013/08/raspberry-pi-wi-fi-honeypot/

https://www.raspberrypi.org/documentation/configuration/wireless/access-point.md

https://www.raspberrypi.org/documentation/remote-access/web-server/nginx.md