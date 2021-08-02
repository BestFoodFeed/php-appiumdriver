<?php

namespace Facebook\WebDriver\Remote;

use Facebook\WebDriver\WebDriverBy;

class RemoteAppiumDriver extends RemoteWebDriver
{
    public function setIsW3cCompliant($value)
    {
        $this->isW3cCompliant = $value;
    }

    public function setImplicitWait(int $ms)
    {
        return $this->executeCustomCommand("/session/:sessionId/timeouts/implicit_wait", 'POST', [
            'ms' => $ms,
        ]);
    }

    public function findElement(WebDriverBy $by, $nullable = false)
    {
        if ($nullable) {
            try {
                return parent::findElement($by);
            } catch (\Throwable $th) {
                return null;
            }
        } else {
            return parent::findElement($by);
        }
    }

    public function getClipboard()
    {
        return $this->executeCustomCommand("/session/:sessionId/appium/device/get_clipboard", 'POST', ['contentType' => 'plaintext']);
    }
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
        $this->setClipboard($text);
        return $this->paste();
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
        return $this->executeCustomCommand("/session/:sessionId/appium/device/push_file", 'POST', ['path' => $path, 'data' => $data]);
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

    public function screenshot()
    {
        return $this->executeCustomCommand("/session/:sessionId/screenshot", 'GET');
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

    public function swipe(RemoteWebElement $element, string $direction, float $percent, int $speed = 10000, string $align = 'center')
    {
        $parameters = [
            'direction' => $direction,
            'percent' => $percent,
            'speed' => $speed,
        ];
        if (in_array($direction, ['left', 'right'])) {
            $parameters['left'] = max($element->getLocation()->getX(), 100);
            $parameters['width'] = min(880, $element->getSize()->getWidth());

            if ($align == 'bottom') {
                $height = round($element->getSize()->getHeight() / 2);
                $parameters['top'] = $element->getLocation()->getY() + $height;
                $parameters['height'] = $height;
            } else if ($align == 'top') {
                $height = round($element->getSize()->getHeight() / 2);
                $parameters['top'] = $element->getLocation()->getY();
                $parameters['height'] = $height;
            } else {
                $parameters['top'] = $element->getLocation()->getY();
                $parameters['height'] = $element->getSize()->getHeight();
            }
        } else {
            $parameters['elementId'] = $element->getID();
        }

        return $this->executeGesture("swipe", $parameters);
    }
    public function drag(int $startX, int $startY, int $endX, int $endY, int $speed = 2500)
    {
        $parameters = [
            'startX' => $startX,
            'startY' => $startY,
            'endX' => $endX,
            'endY' => $endY,
            'speed' => $speed,
        ];

        return $this->executeGesture("drag", $parameters);
    }

    public function pinchClose($area, $percent, $speed = 2500)
    {
        $parameters = [
            "percent" => $percent,
            "speed" => $speed,
        ];
        if (is_array($area)) {
            $parameters = array_merge($parameters, $area);
        } else {
            $parameters['elementId'] = $area;
        }
        return $this->executeGesture("pinchClose", $parameters);
    }

    public function pinchOpen($area, $percent, $speed = 2500)
    {
        $parameters = [
            "percent" => $percent,
            "speed" => $speed,
        ];
        if (is_array($area)) {
            $parameters = array_merge($parameters, $area);
        } else {
            $parameters['elementId'] = $area;
        }
        return $this->executeGesture("pinchOpen", $parameters);
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
            "actions" => [
                [
                    "action" => $action,
                    "options" => $options
                ]
            ]
        ]);
    }

    protected function executeGesture($name, $parameters)
    {
        return $this->executeCustomCommand("/session/:sessionId/execute", 'POST', [
            "script" => "mobile: {$name}Gesture",
            "args" => $parameters
        ]);
    }
}
