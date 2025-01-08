# youtube-comments-channel
Scrape all comments of a channel : videos, streams(livechat + comments), shorts, community posts

**Requirements :**

I use these libraries :<br />
scrapetube (2.5.1) https://github.com/dermasmid/scrapetube<br />
chat_downloader (0.2.8) https://github.com/xenova/chat-downloader<br />
youtube_comment_downloader (0.1.76) https://github.com/egbertbouman/youtube-comment-downloader<br />
youtube_community_tab (0.2.3.2.1) https://github.com/bot-jonas/youtube-community-tab<br />

I installed them with pip.

**Modifications :**

Warning, youtube-community-tab needs some modifications see https://github.com/bot-jonas/youtube-community-tab/issues/4<br />
In site-packages\youtube_community_tab\community_tab.py, change get_community_tab method with this :
```
@staticmethod
def get_community_tab(tabs):
    COMMUNITY_TAB_INDEX = 0

    for tabs_community in tabs:
        if tabs_community["tabRenderer"]["endpoint"]["commandMetadata"]["webCommandMetadata"]["url"].find('community') != -1:
            break
        COMMUNITY_TAB_INDEX = COMMUNITY_TAB_INDEX + 1
        

    if len(tabs) >= COMMUNITY_TAB_INDEX + 1:
        return tabs[COMMUNITY_TAB_INDEX]
    else:
        raise
```

**Code :**

3 different files :
- comment.py : all comments under a video (type : stream, video, shorts)
- chat.py : live chat from a stream
- community.py : comments on posts in Community tab


Output results : 3 separate texts file.
