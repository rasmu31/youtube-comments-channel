# -*- encoding: utf-8 -*-

import scrapetube
from datetime import datetime
from youtube_comment_downloader import *

#Fix Yt update Json
scrapetube.scrapetube.type_property_map['shorts'] = 'reelWatchEndpoint'

urlchannel = "https://www.youtube.com/@Dieutoutpuissantetmodeste"
idchannel = 'UC8Kngruyd5aQM3AALYX7S4w'

file = "comment_" + idchannel + "_" + datetime.fromtimestamp(datetime.now().timestamp()).strftime("%d%m%Y%H%M%S") +  ".txt"
f = open(file, "w", encoding="utf-8")
f.write("Channel " + urlchannel + " id : " + idchannel)
f.write("\n\n")

# get all comments on videos
videostypes = ["shorts", "streams", "videos"]
for videotype in videostypes :
    print("Type : " + videotype)
    f.write("Type : " + videotype)
    f.write("\n\n")
    videos = scrapetube.get_channel(channel_id=idchannel, content_type=videotype)
    for video in videos:
        url = "https://www.youtube.com/watch?v="+str(video['videoId'])
        print(url)
        f.write(url)
        f.write("\n")

        if videotype != "shorts":
            f.write(video['title']['runs'][0]['text'])
            f.write("\n")
        downloader = YoutubeCommentDownloader()
        comments = downloader.get_comments_from_url(url)
        for comment in comments:
            print(datetime.fromtimestamp(comment['time_parsed']).strftime("%d/%m/%Y %H:%M:%S"))
            print(comment['text'])
            f.write(datetime.fromtimestamp(comment['time_parsed']).strftime("%d/%m/%Y %H:%M:%S") + " " + comment['author'] + " (" + comment['channel'] + ")" + ": " + comment['text'])
            f.write("\n")

        f.write("\n")
    f.write("\n")
    
f.write("Done")
f.close()
print("Done")
