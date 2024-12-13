import jieba
import mysql.connector
import datetime
from gensim.models import Word2Vec

def connect_to_database():
    connection = mysql.connector.connect(host='localhost',
                                         user='root',
                                         password='',
                                         database='mooddiary')
    return connection

def retrieve_diary_content(Content, Diary_Id, Day):
    try:
        cursor = Content.cursor()
        sql = "SELECT `Content` FROM `diary` WHERE `Diary_Id` = %s AND `Day` = %s"
        cursor.execute(sql, (Diary_Id, Day))
        result = cursor.fetchall()
        return [row[0] for row in result]
    except Exception as e:
        print("從資料庫中獲取日記內容時出錯:", e)
        return []

def train_word2vec_model(sentences):
    model = Word2Vec(sentences, vector_size=100, window=5, min_count=1, workers=4)
    return model

def load_keywords_from_files(mood_files):
    mood_keywords = {}
    for mood, file_path in mood_files.items():
        try:
            with open(file_path, 'r', encoding='utf-8') as file:
                keywords = [line.strip() for line in file.readlines()]
                mood_keywords[mood] = keywords
        except FileNotFoundError:
            print(f"找不到檔案: {file_path}")
            mood_keywords[mood] = []
    return mood_keywords

def text_analysis(content_list, mood_keywords, word2vec_model):
    mood_count = {mood: 0 for mood in mood_keywords}

    for content in content_list:
        segmented_content = jieba.lcut(content)
        filtered_content = [word for word in segmented_content if len(word) > 1]

        word_vectors = [word2vec_model.wv[word] for word in filtered_content if word in word2vec_model.wv]
        if word_vectors:
            average_vector = sum(word_vectors) / len(word_vectors)
            for mood, mood_words in mood_keywords.items():
                valid_words = [word for word in mood_words if word in word2vec_model.wv]
                if valid_words:
                    mood_vector = sum(word2vec_model.wv[word] for word in valid_words) / len(valid_words)
                    similarity = word2vec_model.wv.cosine_similarities(average_vector, [mood_vector])[0]
                    mood_count[mood] += similarity
                else:
                    print(f"情緒詞 '{mood}' 中的詞在 word2vec_model 中找不到相應的詞向量。")

    total_score = sum(mood_count.values())
    if total_score != 0:
        for mood in mood_count:
            mood_count[mood] /= total_score

    return mood_count

def get_current_diary_id(Content):
    try:
        cursor = Content.cursor()
        sql = "SELECT MAX(`Diary_Id`) FROM `diary`"
        cursor.execute(sql)
        result = cursor.fetchone()
        return result[0] if result[0] else None
    except Exception as e:
        print("獲取當前日記ID時出錯:", e)
        return None

def update_diary_mood(Content, Diary_Id, Day, mood):
    try:
        cursor = Content.cursor()
        sql = "UPDATE `diary` SET `mood` = %s WHERE `Diary_Id` = %s AND `Day` = %s"
        cursor.execute(sql, (mood, Diary_Id, Day))
        Content.commit()
    except Exception as e:
        print("更新日記情緒時出錯:", e)

def main():
    Content = connect_to_database()
    
    Diary_Id = get_current_diary_id(Content)  
    Day = datetime.date.today().strftime("%Y-%m-%d")

    mood_files = {
        '平靜': 'text/calm.txt',
        '害怕': 'text/fear.txt',
        '失落': 'text/sad.txt',
        '喜悅': 'text/joy.txt',
        '幸福': 'text/happiness.txt',
        '生氣': 'text/angry.txt'
    }

    mood_keywords = load_keywords_from_files(mood_files)

    if Diary_Id is not None:
        Diary_content = retrieve_diary_content(Content, Diary_Id, Day)

        if Diary_content:
            sentences = [jieba.lcut(content) for content in Diary_content]
            word2vec_model = train_word2vec_model(sentences)

            mood_count = text_analysis(Diary_content, mood_keywords, word2vec_model)

            dominant_mood = max(mood_count, key=mood_count.get)

            update_diary_mood(Content, Diary_Id, Day, dominant_mood)

            print(dominant_mood)
        else:
            print("未找到指定日記ID在指定日期的日記。")
    else:
        print("無法獲取當前日記ID。")

if __name__ == "__main__":
    main()
