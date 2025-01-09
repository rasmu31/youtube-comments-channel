# youtube-comments-channel
Scrape all comments of a channel : videos, streams(livechat + comments), shorts, community posts

**Limitations :**

Author's name is different in livechat and comments.<br/>
In livechat, it's @something whereas it's the Youtube profile name in comments.<br/>
To overcome this, I added the author's channel ID in parentheses.<br/>
Example for a specific user:<br/>
- in a comment: @LaVeriteQuiDerangeLVQD20 (UC7tNCz_45PF1xJU79i4WaKw)<br/>
- in the chat: La Vérité Qui Dérange LVQD 20 (UC7tNCz_45PF1xJU79i4WaKw)<br/>

So, if you spotted the author @LaVeriteQuiDerangeLVQD20 (UC7tNCz_45PF1xJU79i4WaKw) in a comment, you can find him in the chat by searching for UC7tNCz_45PF1xJU79i4WaKw

In livechat, you can get the author channel id, but not his @something<br />
To get @something, you should make a request for every chat message, so it will be too slow.

**Requirements :**

I use these libraries at the moment :<br />
Python 3.12.5<br />
scrapetube 2.5.1 https://github.com/dermasmid/scrapetube<br />
chat_downloader 0.2.8 https://github.com/xenova/chat-downloader<br />
youtube_comment_downloader 0.1.76 https://github.com/egbertbouman/youtube-comment-downloader<br />
youtube_community_tab 0.2.3.2.1 https://github.com/bot-jonas/youtube-community-tab<br />

I installed them with pip.

**Modifications :**

- Change chat formatting to add channel id of author's message.<br />
See default template in site-packages\chat_downloader\formatting\custom_formats.json<br />
Change default.template value with :<br />
```
"{time_text|timestamp}{author.badges}{money.text}{author.display_name|author.name} ({author.id}){message}"
```

- Warning, youtube-community-tab needs some modifications see https://github.com/bot-jonas/youtube-community-tab/issues/4<br />
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
