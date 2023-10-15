<?php
SELECT x251_news_stories.*, 
       x251_news_topics.topic_title, 
       x251_news_topics.topic_color, 
       x251_news_topics.topic_color_set 

FROM x251_news_stories, x251_news_topics 

WHERE (x251_news_stories.topicid = x251_news_topics.topic_id) 
AND (published > 0 
AND published <= 1697363948) 
AND (expired = 0 OR expired > 1697363948) 
AND topicid IN (20,2,13,23,15,24,17,21,16,26) 
ORDER BY x251_news_topics.topic_title ASC, 
         x251_news_stories.published ASC, 
         x251_news_stories.title ASC

         
         
SELECT x251_news_stories.*, 
       x251_news_topics.topic_title, 
       x251_news_topics.topic_color, 
       x251_news_topics.topic_color_set 

FROM x251_news_stories, x251_news_topics 

WHERE (x251_news_stories.topicid = x251_news_topics.topic_id) 
AND (published > 0 
AND published <= 1697363948) 
AND (expired = 0 OR expired > 1697363948) 
AND topicid IN (20,2,13,23,15,24,17,21,16,26) 
ORDER BY x251_news_topics.topic_title ASC, 
         x251_news_stories.counter DESC, 
         x251_news_stories.published DESC         
?>
