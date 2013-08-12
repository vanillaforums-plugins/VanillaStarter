# VanillaStarter plugin for Vanilla Forums
Fork this repository and start writing your own plugin. Batteries included.

## Quickstart
1. Download it
```
curl -L -o vanilla_starter.zip https://github.com/lifeisfoo/VanillaStarter/archive/master.zip
unzip vanilla_starter.zip
```
2. Customize it
  1. mv VanillaStarter MyPluginName
  2. mv MyPluginName/class.vanillastarter.plugin.php MyPluginName/class.mypluginame.plugin.php
  3. change ```$PluginInfo['VanillaStarter'] = array(``` to ```$PluginInfo['MyPluginName'] = array(```
  4. change ```class VanillaStarterPlugin extends Gdn_Plugin {``` to ```class MyPluginNamePlugin extends Gdn_Plugin {```
  5. change what you want!

## Forking
Feel free to fork and add additional example code and documentation.

Requires Vanilla >= 2.0.18.4


##Author and License
Alessandro Miliucci, GPL v3
