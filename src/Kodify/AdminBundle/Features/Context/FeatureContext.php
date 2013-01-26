<?php

namespace Kodify\AdminBundle\Features\Context;

use Symfony\Component\HttpKernel\KernelInterface;
use Behat\Symfony2Extension\Context\KernelAwareInterface;
use Behat\MinkExtension\Context\MinkContext;

use Behat\Behat\Event\StepEvent;
use Behat\Behat\Context\BehatContext;
use Behat\Behat\Exception\PendingException;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Behat\Behat\Context\Step;
use Behat\Mink\Driver\Selenium2Driver;

use Kodify\AdminBundle\Entity\Pornstar;
use Kodify\AdminBundle\Entity\Tag;
use Kodify\AdminBundle\Entity\Video;
use Kodify\AdminBundle\Entity\Clip;
use \Kodify\AdminBundle\Entity\PutIoFile;

use Kodify\TestsBundle\Features\Context\BaseFeatureContext;

class FeatureContext extends BaseFeatureContext
{
    /**
     * @Given /^I am anonymous user$/
     */
    public function iAmAnonymousUser()
    {

    }

    /**
     * @Given /^a logged in user$/
     */
    public function aLoggedInUser()
    {
        return array(
            new Step\When('I am on "/login"'),
            new Step\When('I fill in "_username" with "admin"'),
            new Step\When('I fill in "_password" with "adminpass"'),
            new Step\When('I press "Sign in"'),
            new Step\Then('I am on "/"'),
        );
    }


    /**
     * @Given /^"([^"]*)" pornstars are created$/
     */
    public function pornstarsAreCreated($qty)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();

        for ($i = 1; $i <= $qty; $i++) {
            $pornstar = new Pornstar();
            $pornstar->setName('name '.$i);
            $pornstar->setAlias('alias '.$i);
            $pornstar->setDescription('description '.$i);
            $pornstar->setTwitter('@name'.$i);
            $pornstar->setEnabled(1);

            $em->persist($pornstar);
        }

        $em->flush();
    }

    /**
     * @Given /^"([^"]*)" tags are created$/
     */
    public function tagsAreCreated($qty)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();

        for ($i = 1; $i <= $qty; $i++) {
            $tag = new Tag();
            $tag->setName('name '.$i);

            $em->persist($tag);
        }

        $em->flush();
    }

    /**
     * @Given /the following video exist:/
     */
    public function theFollowingVideoExist(TableNode $table)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $video = new Video();

            $video->setOriginalName($row['originalfilename']);
            $video->setFilename($row['originalfilename']);

            $dt = new \DateTime();
            if (isset($row['timestamp'])) {
                $dt->setTimestamp($row['timestamp']);
            } else if (isset($row['timeAgo'])) {
                $dt->setTimestamp(time() - $row['timeAgo']);
            }

            $video->setTimestamp($dt);

            if (isset($row['duration'])) {
                $duration = $row['duration'];
            } else {
                $duration = 360000;
            }
            $video->setDurationInMilliseconds($duration);

            $video->setStatus(constant("Kodify\AdminBundle\Entity\\" . $row['status']));

            if (isset($row['blockedBy'])) {
                $video->setBlockedBy($row['blockedBy']);
            }
            if (isset($row['duration'])) {
                $video->setDurationInMilliseconds($row['duration']);
            } else {
                $video->setDurationInMilliseconds(360000);
            }


            $em->persist($video);
        }

        $em->flush();
    }

    /**
     * @Given /^exists (\d+) videos with status "([^"]*)"$/
     */
    public function existsVideosWithStatus($qty, $status)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        for ($i = 0; $i < $qty; $i++) {

            $video = new Video();
            $video->setOriginalName('test');
            $video->setFilename('test');
            $video->setTimestamp(new \DateTime());
            $video->setDurationInMilliseconds(360000);
            $video->setStatus(constant("Kodify\AdminBundle\Entity\\" . $status));
            $video->setDurationInMilliseconds(360000);


            $em->persist($video);
        }

        $em->flush();
    }

    /**
     * @Given /^exists (\d+) PutIoFiles related with Videos with status "([^"]*)" and thumbnailsDeleted = "([^"]*)"$/
     */
    public function existsPutiofilesRelatedWithVideosWithStatusAndThumbnailsdeleted($qty, $videoStatus, $thumbnailsDeleted)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        for ($i = 0; $i < $qty; $i++) {

            $video = new Video();
            $video->setOriginalName('test');
            $video->setFilename('test');
            $video->setTimestamp(new \DateTime());
            $video->setDurationInMilliseconds(360000);
            $video->setStatus(constant("Kodify\AdminBundle\Entity\\" . $videoStatus));
            $video->setDurationInMilliseconds(360000);
            $video->setThumbnailsDeleted($thumbnailsDeleted);

            $putIoFile = new PutIoFile();
            $putIoFile->setPutioId(microtime() * 100000);
            $putIoFile->setName('test');
            $putIoFile->setStatus(PutIoFile::UPLOADING);
            $putIoFile->setVideo($video);
            $putIoFile->setCreatedAt(new \DateTime());
            $putIoFile->setDownloadUrl('test');
            $putIoFile->setSize(10);

            $em->persist($putIoFile);
            $em->persist($video);
        }

        $em->flush();
    }

    /**
     * @Given /^exists (\d+) PutIoFiles related with Videos with status "([^"]*)"$/
     */
    public function existsPutiofilesRelatedWithVideosWithStatus($qty, $videoStatus)
    {
        $this->existsPutiofilesRelatedWithVideosWithStatusAndThumbnailsdeleted($qty, $videoStatus, 0);
    }

    /**
     * @Given /^exists (\d+) PutIoFiles$/
     */
    public function existsPutiofiles($qty)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        for ($i = 0; $i < $qty; $i++) {

            $putIoFile = new PutIoFile();
            $putIoFile->setPutioId(microtime() * 100000);
            $putIoFile->setName('test');
            $putIoFile->setStatus(PutIoFile::UPLOADING);
            $putIoFile->setCreatedAt(new \DateTime());
            $putIoFile->setDownloadUrl('test');
            $putIoFile->setSize(10);

            $em->persist($putIoFile);
        }

        $em->flush();
    }



    /**
     * @Given /the following pornstar exist:/
     */
    public function theFollowingPornstarExist(TableNode $table)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $pornstar = new Pornstar();

            $pornstar->setName($row['name']);
            $pornstar->setEnabled($row['enabled']);

            if (isset($row['thumbnailUrl'])) {
                $pornstar->setThumbnailUrl($row['thumbnailUrl']);
            }

            $em->persist($pornstar);
        }

        $em->flush();
    }

    /**
     * @Given /the following tags exist:/
     */
    public function theFollowingTagsExist(TableNode $table)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $hash = $table->getHash();
        foreach ($hash as $row) {
            $tag = new Tag();

            $tag->setName($row['name']);
            $tag->setEnabled($row['enabled']);

            $em->persist($tag);
        }

        $em->flush();
    }

    /**
     * @Given /the following clip exist:/
     */
    public function theClipExist(TableNode $table)
    {
        $em = $this->kernel->getContainer()->get('doctrine')->getManager();
        $hash = $table->getHash();

        foreach ($hash as $row) {
            $clip = new Clip();
            $clip->setTitle($row['title']);
            $clip->setContentManager($row['contentmanager']);

            $dt = new \DateTime();
            if (isset($row['timestamp'])) {
                $dt->setTimestamp($row['timestamp']);
            } else if (isset($row['timeAgo'])) {
                $dt->setTimestamp(time() - $row['timeAgo']);
            }

            $clip->setTimestamp($dt);
            $clip->setStatus(constant("Kodify\AdminBundle\Entity\\" . $row['status']));

            $startTime = (isset($row['startTime']) ? $row['startTime'] : 0);
            $clip->setStartTime($startTime);

            $endTime = (isset($row['endTime']) ? $row['endTime'] : 0);
            $clip->setEndTime($endTime);

            $tags = (isset($row['tags']) ? $row['tags'] : 'tag 1,tag2,tag 3');
            $clip->setTags($tags);

            $pornstars = (isset($row['pornstars']) ? $row['pornstars'] : 'pornstar 1,pornstar 2');
            $clip->setPornstars($pornstars);

            $em->persist($clip);
        }

        $em->flush();
    }

    /**
     * @When /^The Mixpanel code is mocked$/
     */
    public function theMixpanelCodeIsMocked()
    {
        $this->getSession()->evaluateScript(
            "
            mixPanelMock = {
               calledMethods: [],
               identify: function(param){return true;},
               track: function(param) {
                    this.calledMethods[param] = true;
               }
            }
            mixpanel = mixPanelMock
            "
        );
    }

    /**
     * @Then /^Mixpanel should track the event "([^"]*)"$/
     */
    public function mixpanelShouldTrackTheEvent($event)
    {
        $result = $this->getSession()->evaluateScript(
            "
            if (mixpanel.calledMethods['{$event}']) {
                $('body').addClass('{$event}Test');
            }
            "
        );
        $result = $this->getSession()->getPage()->find('css',"body.{$event}Test");
        if(empty($result)){
            throw new \Exception("{$event} was not tracked");
        }
    }

    /**
     * @Then /^jwplayer should be configured with autostart true$/
     */
    public function jwplayerShouldBeConfiguredWithAutostartTrue()
    {
        $result = $this->getSession()->evaluateScript("return jwplayer('videoPlayer').config.autostart;");

        if($result != "true"){
            throw new \Exception("jwplayer is not configured to autostart");
        }
    }


    /**
     * @Given /^fixtures "([^"]*)" is in "([^"]*)"$/
     */
    public function fixturesIsIn($fixture, $destination)
    {
        $path = $this->kernel->locateResource('@KodifyAdminBundle/Features/Fixtures/' . $fixture);
        $destination = $this->kernel->getContainer()->getParameter('kernel.root_dir');

        copy($path, $destination . '/../web/' . $fixture);
    }
}
