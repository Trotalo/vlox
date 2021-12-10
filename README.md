# kraken

Kraken it's an extra (aka plugin) designed to work with the [CMS ModX](https://modx.com/) to easily integrate VueJS and let you 
easily create digital experiences in a simple and straightforward way.

By integrating a powerful development framework focused on content management, and Vue, you'll get most of the complex 
functionalities, like security management, webservices, out of the box, while being free to build any kind of user 
interface interactions that you can imagine.

## Using kraken

You'll need a [Modx installation](https://docs.modx.com/current/en/getting-started/installation), if you want, you can
dive into the ModX documentation, or just use the docker configuration that we have to launch a fully working 
infrastructure.

[Here](https://github.com/modxMonster/modxBaseEnviroment/blob/master/README.md) you can find details on how to set up 
a docker configuration on W10 using WSL2

1. Run the following command `git clone git@github.com:modxMonster/modxBaseEnviroment.git yourProjectName` DON'T forget
to update the `yourProjectName` value
2. Edit the `docker-compose.yml` file and set the `YOUR_IP` to your local ip address
3. Enter yourProjectName folder, and execute the command `docker-compose up`
4. Go for a coffee, depending on your connection this can take some time, as mysql and apache services are being 
downloaded and configured
5. Once you see `AH00094: Command line: 'apache2 -D FOREGROUND'` in the console, the service will be up and running.
6. Open `https://YOUR_IP` on your browser, and you should see the ModX welcome page
7. Click the ***Go to the manager*** big green button
8. Enter the manager using admin/admin as credentials

At this point you can install kraken in 2 possible ways, first you can just install the extra and start developing 
your site, or you can configure the project to develop new features or view the kraken code.

###Installing the extra to build my site
1. Download [the package file](https://github.com/modxMonster/kraken/raw/master/_packages/kraken-0.0.1-pl.transport.zip)
2. Enter the ModX manager using the browser and open https://YOUR_IP/manager
3. In the top menu, Go to `extras -> installer` this will take you to the **Package Management** page
4. Press the arrow next to the **Download Extras** button, this will open a small menu
5. From the menu select the option that reads **Upload a package**
6. Click the choose file button, and use the file browser to locate the `kraken-0.0.1-pl.transport.zip` file that you 
downloaded on the first step
7. Once the file is loaded into the window, press **Upload** then **Close**
8. Now you'll see `kraken` in the list, and a **Not Installed** message next to it
9. Click the **Install** button below the text `kraken`
10. Press continue to install the package.
11. Refresh the page
12. Check the new **Monster** option of the top menu
13. Now you are all set to start working

###Install the project to work on kraken
1. We need to install the [amazing GPM](https://github.com/theboxer/Git-Package-Management) to be able to easily build 
a ModX extra
2. Download the [package file](https://github.com/theboxer/Git-Package-Management/raw/master/_packages/gitpackagemanagement-0.14.0-alpha9.transport.zip)
3. In the top menu, Go to `extras -> installer` this will take you to the **Package Management** page
4. Press the arrow next to the **Download Extras** button, this will open a small menu
5. From the menu select the option that reads **Upload a package**
6. Click the choose file button, and use the file browser to locate the `gitpackagemanagement-0.14.0-alpha9.transport.zip` file that you
   downloaded on the second step
7. Once the file is loaded into the window, press **Upload** then **Close**
8. Now you'll see `gitpackagemanagement` in the list, and a **Not Installed** message next to it
9. Click the **Install** button below the text `gitpackagemanagement`
10. Press **Setup Options** to continue
11. On the pop-up window type `/var/www/html` in the **Packages directory** field
12. Press **Install Package**
13. Open a terminal and go to `yourProjectName` folder
14. Go to the `www/html` folder
15. Run the following commands to fix folders permissions for development
``` 
    sudo chown -R www-data:www-data ../*
    sudo chmod -R 0777 * ../*
```
16. Clone the kraken repository by running `git clone git@github.com:modxMonster/kraken.git`
17. Now we need to install the PHP dependencies, for this you need to retrieve the name of the docker container, for 
run `docker ps`
18. You should see an entry similar to `yourprojectname_web_1` with the name you assigned, copy that entry
19. Update the **yourprojectname** value and run the following command, this will run composer inside the container
and install the dependencies
`docker exec -ti yourprojectname_web_1 sh -c "cd /var/www/html/kraken/core/components/kraken/controllers && composer install"`
20. Refresh the page
21. Now we need to install the kraken extra, using GPM, go to `Extras -> Git Package Management`
22. Click the **Add package** button
23. In the popup window type `kraken` inside the **Folder** text input and press the **save** option
24. If you experience any permission errors, run step 15 again.
25. Refresh the page
26. Check the new **Monster** option of the top menu
27. Now you are all set to start working

###Soo, now what?
Once we got our extra installed, up and running, lets create our first block!
1. Go to the `Monster -> Kraken`
2. Press **RELEASE THE KRAKEN** button
3. Press **CREATE A NEW BLOCK** button
4. In the popup window, type `myNewComponent` as the **Block name**, and you can leave the description empty
5. We are going to use some bootstrap vue elements to create a responsive header, paste the following code
in the HTML section of the page you are on.
```
<template>
  <b-navbar id="headerTrotalo" toggleable="lg" fixed="top" class="wider-block-width">
    <b-navbar-brand href="#">
      <b-img src="https://png.pngtree.com/png-vector/20210110/ourmid/pngtree-hello-world-svg-design-png-image_2719857.jpg"
                     class="d-inline-block align-top" alt="yourLogo" fixed="top"></b-img>
    </b-navbar-brand>
    <b-navbar-toggle target="nav-collapse"></b-navbar-toggle>
    <b-collapse id="nav-collapse" is-nav>
      <b-navbar-nav class="ml-auto">
        <b-nav-item href="#">Option 1</b-nav-item>
        <b-nav-item href="#">Option 2</b-nav-item>
        <b-nav-item href="#">Option 3</b-nav-item>
        <b-nav-item href="#">Option 4</b-nav-item>
      </b-navbar-nav>
    </b-collapse>
  </b-navbar>
</template>
<script>
  module.exports = {
    name: "[[+componentName]]",
    data() {
      return {
        krakenBlock: [[+blockContent]]
      };
    },
  };
</script>
<style scope>
  #headerTrotalo {
    background-color: #1C1C1C;
    color: white;
    border-radius: 0px 0px 10px 10px;
    img {
      max-height: 50px;
    }
    .nav-link{
      color: white;
    }
  }
</style>
```
6. Press **SAVE&PUBLISH**
7. Go to the **Resources** section on the left-hand panel
8. Click the plus sign next to the **Website** text, this will open up the **New Document** window
In ModX, resources equals pages in your website, name your new page
9. Select the **KrakenTemplate** option from the **Uses Template** drop down
10. Click yes on the warning popup
11. Click **Save** on the upper right section of the page
12. Click the **KrakenBlocks** option next to **Resource Groups**
13. Click the **ADD BLOCK** button
14. Select the component that we created
15. You see the component inserted on the preview window
16. Right-click on the resource you created on step 8
17. Click the **View** option
18. A new browser tab will open and show you the final view of the page



 

