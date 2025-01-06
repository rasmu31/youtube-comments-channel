# -*- encoding: utf-8 -*-

# Community Posts url https://github.com/bot-jonas/youtube-community-tab
# Tweek COMMUNITY_TAB_INDEX in \site-packages\youtube_community_tab\community_tab.py
# OR change code https://github.com/bot-jonas/youtube-community-tab/issues/4 => I did this

# Comments https://github.com/egbertbouman/youtube-comment-downloader/

from youtube_community_tab.community_tab import CommunityTab
from youtube_comment_downloader import *
import json
from datetime import datetime

urlchannel = "https://www.youtube.com/@Dieutoutpuissantetmodeste"
idchannel = 'UC8Kngruyd5aQM3AALYX7S4w'

f = open("community_" + idchannel + ".txt", "w", encoding="utf-8")
f.write("Channel " + urlchannel + " id : " + idchannel)
f.write("\n\n")

# Cache expiration
EXPIRATION_TIME = 1 * 60 * 60

ct = CommunityTab(idchannel)
ct.load_posts(expire_after=EXPIRATION_TIME)

# Load more posts
while(ct.posts_continuation_token):
    ct.load_posts(expire_after=EXPIRATION_TIME)

print(str(len(ct.posts))+ " posts")

for post in ct.posts:
    url = "https://youtube.com/post/" + post.post_id
    print(url)
    f.write(url)
    f.write("\n")

    # Comments function from youtube_community_tab isn't working, so I use youtube_comment_downloader
    downloader = YoutubeCommentDownloader()
    comments = downloader.get_comments_from_url(url, sort_by=SORT_BY_RECENT)

    for comment in comments:
        print(datetime.fromtimestamp(comment['time_parsed']).strftime("%d/%m/%Y %H:%M:%S"))
        print(comment['text'])
        f.write(comment['author'] + "(" + datetime.fromtimestamp(comment['time_parsed']).strftime("%d/%m/%Y %H:%M:%S") + ")" + " : " + comment['text'])
        f.write("\n")

    f.write("\n")

f.write("Done")
f.close()
print("Done")
