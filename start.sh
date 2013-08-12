#!/bin/sh
echo "Downloading skeleton..."
curl -sL -o vanilla_starter.zip https://github.com/lifeisfoo/VanillaStarter/archive/master.zip
unzip vanilla_starter.zip
rm vanilla_starter.zip
echo "Please enter plugin name (whitespaces accepted): "
read PLUGIN_NAME
TRIMMED_NAME=$(echo $PLUGIN_NAME | tr -d ' ')
LOWER_NAME=$(echo $TRIMMED_NAME | tr '[:upper:]' '[:lower:]')
mv VanillaStarter-master $TRIMMED_NAME
CLASS_FILE=$TRIMMED_NAME/class.$LOWER_NAME.plugin.php
mv $TRIMMED_NAME/class.vanillastarter.plugin.php $CLASS_FILE
sed -i.bak s/VanillaStarter/$TRIMMED_NAME/g $CLASS_FILE
rm $CLASS_FILE.bak
echo "Plugin created inside $TRIMMED_NAME/ directory"
