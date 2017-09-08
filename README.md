# osticket-not-yours-plugin
Tells an agent viewing a ticket that this ticket is actually owned by someone else, makes it SUPER OBVIOUS.

![Preview Image](http://i.imgur.com/4Gtcubi.png)

Just install/enable and watch the colours fly!

Uses injected javascript to edit a page if the ticket you are viewing has been assigned to another agent. 

The fuscia colour is admin-editable. (Can change warning and normal background colours, the warning is used for the background and flashing notice). 

# Relies on 
https://github.com/clonemeagain/attachment_preview

We use the API functionality in attachment_preview to inject code into the DOM without doing too much in this plugin.
