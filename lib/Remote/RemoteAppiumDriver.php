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

    public function type($text)
    {
        $this->driver->setClipboard($text);
        return $this->driver->paste();
    }

    public function back()
    {
        return $this->executeShell("input keyevent 4");
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

    public function getDisplayDensity()
    {
        return $this->executeCustomCommand("/session/:sessionId/appium/device/display_density", 'GET');
    }

    public function startActivity($package, $activity)
    {
        return $this->executeCustomCommand("/session/:sessionId/appium/device/start_activity", 'POST', [
            'appPackage' => $package,
            'appActivity' => $activity
        ]);
    }
    public function currentActivity()
    {
        return $this->executeCustomCommand("/session/:sessionId/appium/device/current_activity", 'GET');
    }

    public function executeScroll($strategy, $selector, $elementId = null, $maxSwipes = null)
    {
        $args = [
            'strategy' => $strategy,
            'selector' => $selector,
        ];
        if ($elementId) {
            $args['elementId'] = $elementId;
        }
        if ($maxSwipes) {
            $args['maxSwipes'] = $maxSwipes;
        }

        return $this->executeCustomCommand("/session/:sessionId/execute", 'POST', [
            "script" => "mobile: scroll",
            "args" => $args
        ]);

    }

    public function executeStartActivity($intent, array $parameters = [])
    {
        $parameters['intent'] = $intent;
        return $this->executeCustomCommand("/session/:sessionId/execute", 'POST', [
            "script" => "mobile: startActivity",
            "args" => $parameters
        ]);

    }

    public function tap($x, $y)
    {
        return $this->performTouch('tap', ['x' => $x, 'y' => $y]);
    }

    protected function performTouch($action, array $options)
    {
        return $this->executeCustomCommand("/session/:sessionId/touch/perform", 'POST', [
            "action" => $action,
            "options" => $options
        ]);
    }
}
