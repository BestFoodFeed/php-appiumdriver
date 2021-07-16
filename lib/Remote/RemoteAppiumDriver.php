<?php

namespace Facebook\WebDriver\Remote;

class RemoteAppiumDriver extends RemoteWebDriver
{
    public function setClipboard($text)
    {
        return $this->executeCustomCommand("/session/:sessionId/appium/device/set_clipboard", 'POST', ['content' => base64_encode($text)]);
    }

    public function paste()
    {
        return $this->executeShell("input keyevent 279");
    }

    public function executeShell($command)
    {
        return $this->executeCustomCommand("/session/:sessionId/execute", 'POST', [
            "script" => "mobile: shell",
            "args" => ["command" => $command]
        ]);
    }

    public function pushFile(string $path, string $data)
    {
        return $this->executeCustomCommand("/session/:sessionId/appium/device/push_file",'POST', ['path' => $path, 'data' => $data]);
    }
}
