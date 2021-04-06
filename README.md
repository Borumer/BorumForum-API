# Borum Forum API

[![Contributors][contributors-shield]](https://github.com/Borumer/BorumForum-API/graphs/contributors)
[![Forks][forks-shield]](https://github.com/Borumer/BorumForum-API/network/members)
[![Stargazers][stars-shield]](https://github.com/Borumer/BorumForum-API/stargazers)
[![Issues][issues-shield]](https://github.com/Borumer/BorumForum-API/issues)
[![MIT License][license-shield]](https://github.com/Borumer/BorumForum-API/blob/master/LICENSE)

This is a REST API powered by Vercel and PHP that provides the main functions you'd expect from a forum, such as posting, voting, an admin system, etc.

---

## Features

The below features are planned for the first release.

-   Question posting, editing, voting, and deleting
-   Answer posting, editing, voting, and deleting
-   Commenting
-   Private messaging
-   Message deleting (for admins)
-   Ability to ban users (for admins)

### Admin Level

When an API key associated with an admin is sent, the API gives access to deletion and editing of all kinds of posts without required authentication from the user who made the posts. 

## Setup

Clone this repo to your desktop and run `composer install` to install all the dependencies.

You might want to look into `config.json` to make change the port you want to use and set up a SSL certificate.

## Usage

Make all requests to https://api.borumtech.com/api/v1

After the `v1/` is the name of the endpoint. Requests MUST be sent with an authorization key for any OK responses to return.

## Built With

- vercel-php
- [Vercel](https://vercel.com) - Hosting platform
- [PHP]() - scripting language
- [php-sleep](https://github.com/Borumer/php-sleep) - 

## License

> You can check out the [full license](./LICENSE)

This project is licensed under the terms of the **MIT** license.

<!-- MARKDOWN LINKS & IMAGES -->
<!-- https://www.markdownguide.org/basic-syntax/#reference-style-links -->
[contributors-shield]: https://img.shields.io/github/contributors/Borumer/BorumForum-API.svg?style=for-the-badge
[forks-shield]: https://img.shields.io/github/forks/Borumer/BorumForum-API.svg?style=for-the-badge
[stars-shield]: https://img.shields.io/github/stars/Borumer/BorumForum-API.svg?style=for-the-badge
[issues-shield]: https://img.shields.io/github/issues/Borumer/BorumForum-API.svg?style=for-the-badge
[license-shield]: https://img.shields.io/github/license/othneildrew/Best-README-Template.svg?style=for-the-badge
[product-screenshot]: images/screenshot.png

## Contributing

So you'd like to contribute to the Borum Forum API? Excellent! Thank you very much. I can absolutely use your help. Follow the steps below:

1. Read the [contributing guidelines](CONTRIBUTING.md).
2. Open an issue or pull request preferably using an existing template

