# -*- encoding: utf-8 -*-

from chat_downloader import ChatDownloader
import scrapetube
import sys

urlchannel = "https://www.youtube.com/@Dieutoutpuissantetmodeste"
idchannel = 'UC8Kngruyd5aQM3AALYX7S4w'

f = open("chat_" + idchannel + ".txt", "w", encoding="utf-8")
f.write("Channel " + urlchannel + " id : " + idchannel)
f.write("\n\n")

#get all url streams
streams = scrapetube.get_channel(channel_id=idchannel, content_type="streams")

print("STREAMS")
for stream in streams:
    url = "https://www.youtube.com/watch?v="+str(stream['videoId'])
    f.write(url)
    f.write("\n")
    f.write(stream['title']['runs'][0]['text'])
    f.write("\n")
    print(url)
    try:
        chat = ChatDownloader().get_chat(url)       # create a generator
        for message in chat:                        # iterate over messages
            print(chat.format(message))
            f.write(chat.format(message))
            f.write("\n")
    except Exception as ex:
        f.write(str(ex))
        f.write("\n")

    f.write("\n")

f.write("Done")
f.close()
print("Done")
