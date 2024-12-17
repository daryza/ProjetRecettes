#!/usr/bin/env python
"""
Script pour tester la traduction des 'à propos' en anglais et espagnol.
"""
# Imports external libraries
import requests
import re

# Import local code
# from cairn.models import databases

def setup(app, cli_group):
    config_deepl = app.config.deepl  # Récupération du token DeepL
    token_deepl = config_deepl.token_deepl  # Clé d'authentification DeepL

    def init_session_deepl(token):
        # Initialisation de la session avec DeepL
        session = requests.Session()
        session.headers.update({"Authorization": f"DeepL-Auth-Key {token}"})
        return session

    def translate_text(session, text, target_lang):
        """
        Traduit le texte dans la langue cible à l'aide de DeepL.
        """
        if not text:
            return None
        response = session.post(
            "https://api.deepl.com/v2/translate",
            data={"text": text, "target_lang": target_lang},
        )
        response.raise_for_status()
        translations = response.json().get("translations")
        if translations:
            return translations[0].get("text")
        return None

    @cli_group.command("translate-revue-a-propos")
    def test_translate_revue_a_propos():
        """
        Commande pour tester la traduction des 'à propos'.
        """
        # Initialisation de la session DeepL
        session = init_session_deepl(token_deepl)

        # Connexion à la base de données (pas de modification, uniquement lecture)
        db = databases.local_cairn4_pub

        # Récupérer les lignes correspondant à un id_revue donné (exemple : "STA")
        id_revue_fr = "STA"
        id_revue_en = f"e_{id_revue_fr}"
        id_revue_es = f"s_{id_revue_fr}"

        articles = db.execute(
            """
            SELECT *
            FROM revue_a_propos
            WHERE id_revue IN (:id_revue_fr, :id_revue_en, :id_revue_es)
            """,
            {"id_revue_fr": id_revue_fr, "id_revue_en": id_revue_en, "id_revue_es": id_revue_es},
        ).fetchall()

        # Afficher les lignes avant traduction
        print("=== Lignes AVANT traduction ===")
        for article in articles:
            print(dict(article))

        # Traduire le texte pour les versions "e_" et "s_"
        for article in articles:
            if article["id_revue"] == id_revue_fr:
                original_text = article["texte"]

                # Nettoyage du texte
                cleaned_text = re.sub(r"\s+", " ", original_text.strip())

                # Traductions
                try:
                    translated_text_en = translate_text(session, cleaned_text, "EN")
                except Exception as e:
                    print(f"Erreur lors de la traduction en anglais pour ID {article['id']}: {e}")
                    translated_text_en = None

                try:
                    translated_text_es = translate_text(session, cleaned_text, "ES")
                except Exception as e:
                    print(f"Erreur lors de la traduction en espagnol pour ID {article['id']}: {e}")
                    translated_text_es = None

        # Simuler les lignes après traduction
        print("\n=== Lignes APRÈS traduction ===")
        for article in articles:
            updated_article = dict(article)  # Créer une copie pour la simulation

            if article["id_revue"] == id_revue_en and translated_text_en:
                updated_article["texte"] = translated_text_en

            if article["id_revue"] == id_revue_es and translated_text_es:
                updated_article["texte"] = translated_text_es

            print(updated_article)
