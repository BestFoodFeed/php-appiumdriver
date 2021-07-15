<?php

namespace Facebook\WebDriver\Remote;

class RemoteAppiumDriver extends RemoteWebDriver {
    public function setClipboard($text)
    {
        $this->executeCustomCommand("/session/:sessionId/appium/device/set_clipboard",'POST',['content' => base64_encode($text)]);
    }
}