<?php
include "php-functionality/sessions.php";
include "header.php";
include "menu.php";

?>
<main>
    <div id="about-container" class=main-container">
        <div id="about-nav-container">
            <nav id="about-nav">
                <ul>
                    <li><span class="about-link" onclick="aboutShow('about-me');">About Me</span></li>
                    <li><span class="about-link" onclick="aboutShow('about-blog');">About this Blog</span></li>
                    <li><span class="about-link" onclick="aboutShow('about-project');">About the project</span></li>
                </ul>
            </nav>
        </div>
        <div id="about-display-container" class="main-container">
            <article id="about-me" class="about-article">
                <h2>About Me</h2>
                <p>An elder millennial / xennial nerd, born and raised in Germany, now living at the other end of the world in Aotearoa (New Zealand) with my wife and our daughter. After a good decade of working in tourism, I made the change to a more technical role (with work hours and days that are a bit more amenable to family and social life, too). I am further studying (formally and otherwise) towards programming of any kind, especially web development. This blog is a project of mine - I have not only written the contents, but also the website and content management system - see "About the project" for more details.</p>
                <p>My tastes and interests run across a wide variety of things - gaming (both PC and pen &amp; paper roleplaying), travel, outdoors, technology, politics and finance, linguistics and etymology, science, science fiction, social issues and history... you name it, I am probably at least moderately interested. In particular, I love figuring out how things work and how to make them work better, whether those <em>things</em> are social structures, rules for a roleplaying game, data processing or a road trip.</p>
                <p>I am passionate about many environmental and social causes, but inherently optimistic - I believe that change and technology are tools that can help us solve many of the problems we face today, as long as we are willing to use them.</p>
            </article>
            <article id="about-blog" class="about-article">
                <h2>About this blog</h2>
                <p>It's a blog - a soapbox for me to talk about things I am interested in, and hopefully interest, entertain and maybe even help some people.</p>
                <p>It covers a variety of topics from gaming and programming to finance and civics. There is considerable topic drift, and that may not be for everyone - and that is fine. Personally, I enjoy following media that can switch between topics, themes and moods, and that is what I am going for here.</p>
                <p><em>Weltwander</em> is German for &quot;World Walker&quot;, and while I originally chose the name for a long-since abandoned travel blog, it also fits the idea of something that covers many different &quot;worlds&quot;.</p>
            </article>
            <article id="about-project" class="about-article">
                <h2>About this project</h2>
                <p>This blog does not run on any commercial or otherwise pre-built platform. I have built it from scratch, mostly as a challenge and for practice. It is very much a work in progress - I deployed it with only the most barebones features, and many others are planned but not yet fully implemented.</p>
                <p>The entire source code (aside from hardcoded authentication details) is accessible on <a href="https://github.com/Weltwandler/WeltwandlersBlog/upload/main">Github</a> (or will be when I get around to it).</p>
                <p>It is designed to allow for multiple different authors, scheduling posts to be published in the future or even unpublished at a certain time (for temporary announcements and the like). For security reasons, posts are stripped of any HTML, JavaScript and PHP content, but links, images and basic formatting remain functional. Planned features that are not yet fully implemented are an account management system, comments and categories - watch this space.</p>
            </article>
        </div>
    </div>
</main>
<?php
include "footer.php";
?>