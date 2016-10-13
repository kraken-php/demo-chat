# Kraken Demo Application - Chat

[![Total Downloads](https://poser.pugx.org/kraken-php/demo-chat/downloads)](https://packagist.org/packages/kraken-php/demo-chat) 
[![Latest Stable Version](https://poser.pugx.org/kraken-php/framework/v/stable)](https://packagist.org/packages/kraken-php/framework) 
[![Latest Unstable Version](https://poser.pugx.org/kraken-php/framework/v/unstable)](https://packagist.org/packages/kraken-php/framework) 
[![License](https://poser.pugx.org/kraken-php/framework/license)](https://packagist.org/packages/kraken-php/framework)
[![Gitter](https://badges.gitter.im/kraken-php/framework.svg)](https://gitter.im/kraken-php/framework?utm_source=badge&utm_medium=badge&utm_campaign=pr-badge)
[![@kraken_php on Twitter](https://img.shields.io/badge/twitter-%40kraken__php-blue.svg)](https://twitter.com/kraken_php)

> **Note:** This repository contains pre-configured distributed chat application based on [Kraken Framework](https://github.com/kraken-php/framework).

<br>
<p align="center">
<img src="https://avatars2.githubusercontent.com/u/15938282?v=3&s=150" />
</p>

## Description

This repository demonstrates exemplary implementation of chat using HTTP and Websocket servers in PHP using [Kraken Framework](https://github.com/kraken-php/framework) components.

## Architecture

<p align="center">
<img src="https://docs.google.com/uc?export=download&id=0B_FVuB10kPjVWlZMeDFRaDBoTE0" width="453" height="261" />
</p>

## Screenshots

<p align="center">
<img src="https://docs.google.com/uc?export=download&id=0B_FVuB10kPjVOC1UM1hvaVNPS2M" width="880" height="512" />
</p>

## Requirements

* PHP-5.5, PHP-5.6 or PHP-7.0+,
* [Pthreads](http://php.net/manual/en/book.pthreads.php) extension enabled (only if you want to use threading),
* UNIX or ~~Windows~~ OS.

## Installation and Official Documentation

To install this application skeleton, please go to desired location to store project, then call composer:

```
composer create-project --prefer-dist kraken-php/demo-chat .
```

Documentation for the framework can be found in the [official documentation][2] page.

## Starting Project

### Basic Start

To start project, first run `kraken.server` instance.

    $> php kraken.server

Then, check if connection is working in another terminal window:

    $> php kraken server:ping

If everything works correctly, as final step run the application using:

    $> php kraken project:create

After project has been created successfully, go to `http://localhost:6080` address in your browser and you should be able
to see and use examplary chat.

To close whole project, use:

    $> php kraken project:destroy

If you have problems with configuring console-server connection, you can also try alternative start.

### Alternative Start

To start project directly, without console support, use:

    $> php ./data/autorun/kraken.process undefined HttpBroker HttpBroker

**WARNING** This method will be deprecated in upcoming ver 0.4.

## Contributing

This library is pre-configured project application for Kraken Framework. To make contributions, please go to [framework repository][3].

## License

Kraken Framework is open-sourced software licensed under the [MIT license][6]. The documentation is provided under [FDL-1.3 license][7].

[1]: http://kraken-php.com
[2]: http://kraken-php.com/docs
[3]: http://kraken-php.com/getting_started
[4]: http://kraken-php.com/faq
[5]: http://kraken-php.com/docs/contributions
[6]: http://opensource.org/licenses/MIT
[7]: https://www.gnu.org/licenses/fdl-1.3.en.html
[8]: https://groups.google.com/forum/#!forum/kraken-php
