OAuth2 Component
=============================

This is an OAuth2 Component to authenticate users in your
webapplication with Any OAuth2 providers.

Generating the documentation
=============================

To generate the documentation follow the steps:

 * First copy the config/phpdoc.dist.xml.sample to config/phpdoc.dist.xml

 * Install phpdoc in the linux box 

 * See the url :

```
http://phpdoc.org/
```
 * Edit the ṕhpdoc.dist.xml to look like this for example

 ```
 <?xml version="1.0" encoding="UTF-8" ?>
<phpdoc>
    <title>OAuth2 Component</title>
    <parser>
        <target>Use here a full path to a writable dir</target>
        <markers>
            <item>TODO</item>
            <item>FIXME</item>
        </markers>
        <extensions>
            <extension>php</extension>
        </extensions>
        <visibility></visibility>
    </parser>
    <transformer>
        <target>Use here where you want to store the docs</target>
    </transformer>
    <logging>
        <level>debug</level>
        <paths>
            <default>/home/user/tmp/oauth2/docs_log/{DATE}.log</default>
            <errors>/home/user/tmp/oauth2/docs_log/{DATE}.errors.log</errors>
        </paths>
    </logging>
    <transformations>
        <template name="responsive" />
    </transformations>
    <files>
        <directory>Src dir where the src code lives</directory>
    </files>
</phpdoc>
 ```
  * The inside the config directory execute
  ```
  phpdoc -c phpdoc.dist.xml
  ```
  