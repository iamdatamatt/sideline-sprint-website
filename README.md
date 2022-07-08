![Sideline Sprint logo](/img/text-logo-large.png)

![Sideline Sprint newsletter](/img/newsletter.png)

# Sideline Sprint Main Website

## What was Sideline Sprint?

Sideline Sprint was a daily sports email newsletter. The goal was to deliver the best of the sports world, directly to your inbox every weekday morning. Sideline Sprint was in operation from the beginning of 2021 up until March of 2022.

### What was my role?

I served as a co-founder & the tech lead for the project (but also took on many other tasks as is often the case in startups).

### What did I do?

During my time working on Sideline Sprint, I worked on a variety of unique tasks including but not limited to:
- Build a website from scratch and leveraged cloud infrastructure so scaling was effortless
- Ensured email alignment on DMARC/DKIM/SPF/BIMI to achieve best-in-class deliverability with an average open rate of ~50%
- Posted all newsletters as articles to our site using Ghost to promote SEO
- Monitored SEO and improved ranking/clicks drastically over our 1.5 years
- Built a referral program from scratch so that readers could bring in others at a low cost per acquisition (CPA)
- Designed the logo and graphics for the website, as well as merchandise for the referral program
- Created a custom email template that looked great across all devices and setup a custom newsletter writing platform based on TinyMCE
- Setup & administered all of the tools listed in the below sections

## What does this repo contain?
This repo contains code for the main website of Sideline Sprint (i.e. what you would see when you visited https://www.sidelinesprint.com). This includes the tools used for our signup flow, pages for our referral program, and more. The front-end work leveraged the Bootstrap framework and the backend scripting was done using PHP.

## Packages used:

- [Hashids](https://github.com/vinkla/hashids) (generating unique user IDs)
- [Postmark PHP SDK](https://github.com/ActiveCampaign/postmark-php) (interacting with Postmark for sending confirmation emails)
- [Bootstrap](https://getbootstrap.com/) (design of website)
- [Ghost CMS](https://ghost.org/) (hosting our newsletters published to a blog for SEO purposes)
- [Mailjet Markup Language (MJML)](https://mjml.io/) (creating our email templates)

## Other tools I used

- [DigitalOcean](https://www.digitalocean.com/) (cloud hosting platform for our website and database)
- [Google Search Console](https://search.google.com/search-console/about) (SEO & search monitoring)
- [Google Analytics](https://analytics.google.com/) (monitoring website traffic & acquisition)
- [Google Postmaster Tools](https://www.gmail.com/postmaster/) (monitoring email authentication & deliverability)
- [DMARC Digests](https://dmarcdigests.com/) (monitoring email authentication & deliverability)
- [MailerLite](https://www.mailerlite.com/) (first email provider, used for a few months)
- [Campaign Monitor](https://www.campaignmonitor.com/) (second email provider, used for approximately 1 year)
- [Beehiiv](https://www.beehiiv.com/) (third & final email provider, used for a few months)
- [Google Workspace](https://workspace.google.com/) (collaboration amongst staff)
- [Google Domains](https://domains.google/) (website registration)
- [Bunny CDN](https://bunny.net/) (serving static assets to website)
- [Bitwarden](https://bitwarden.com/) (sharing of passwords amongst staff)
- [Auth0](https://auth0.com/) (access management for staff tools website)
- [Postmark](https://postmarkapp.com/) (transactional emails)
- [Ahrefs](https://ahrefs.com/) (SEO monitoring)
- [Google Ads](https://ads.google.com/home/) (advertising campaigns)
- [Reddit Ads](https://ads.reddit.com/) (advertising campaigns)
- [Affinity Photo & Designer](https://affinity.serif.com/en-us/) (logo design, website graphics, social graphics)

If you're interested in seeing the other work I did for Sideline Sprint, please take a look at the following repos:
- [Sideline Sprint Tools Website](https://github.com/mrtrombley/sideline-sprint-tools)
- [Sideline Sprint Miscellaneous Tools](https://github.com/mrtrombley/sideline-sprint-misc)

## Architecture
![Sideline Sprint architecture diagram](/img/architecture-diagram.png)

## Screenshots

### Desktop Homepage
![Sideline Sprint desktop homepage](/img/desktop-homepage.png)

### Mobile Homepage
<img src="/img/mobile-homepage.png" width="300">

### News Article Homepage
![Sideline Sprint news homepage](/img/news-homepage.png)

### News Article
![Sideline Sprint news article](/img/news-article.png)