import redis # a installer avec 'pip install redis' ou 'conda install redis' depuis votre repertoire racine
import time
import sys

# Connexion à Redis
redis_client = redis.StrictRedis(host='localhost', port=6379, db=0)

def autoriser_connexion_utilisateur(id_utilisateur):
    # Récupérer le timestamp actuel
    now = int(time.time())

    # Vérifier les connexions dans les 10 dernières minutes
    recent_connexions = redis_client.zcount(f"utilisateur:{id_utilisateur}:connexions_timestamp", now - 600, now)


    # Si le nombre de connexions dans la fenêtre de 10 minutes est supérieur ou egal à 10
    if recent_connexions >= 10:
        # Vérifier si la dernière connexion a eu lieu il y a plus de 2 minutes après le refus
        last_connexion_time = redis_client.zrange(f"utilisateur:{id_utilisateur}:connexions_timestamp", -1, -1, withscores=True)
        if last_connexion_time and now - last_connexion_time[0][1] >= 120:
            # Réinitialiser le nombre de connexions et autoriser la connexion
            redis_client.delete(f"utilisateur:{id_utilisateur}:connexions_timestamp")
            redis_client.incr(f"utilisateur:{id_utilisateur}:connexions")
            redis_client.zadd(f"utilisateur:{id_utilisateur}:connexions_timestamp", {now: now})
            redis_client.hset("utilisateurs_connectes", id_utilisateur, 1)  # Réinitialiser le compteur dans le hset
            return True
        else:
            # Si la dernière connexion est dans les 2 dernières minutes, refuser la connexion
            return False
    
    # Si le nombre de connexions dans les 10 dernières minutes est inférieur à 10
    # Autoriser la connexion
    redis_client.incr(f"utilisateur:{id_utilisateur}:connexions")
    redis_client.zadd(f"utilisateur:{id_utilisateur}:connexions_timestamp", {now: now})
    redis_client.hset("utilisateurs_connectes", id_utilisateur, recent_connexions + 1)
    return True

    
# Exemple d'utilisation
if __name__ == "__main__":
    # Récupérer l'ID de l'utilisateur passé en argument
    id_utilisateur = sys.argv[1]
    if autoriser_connexion_utilisateur(id_utilisateur):
        print("Connexion autorisee!")
    else:
        print("Connexion refusee - Nombre maximal de connexions dans les 10 dernieres minutes atteint. Reessayez dans 2 minutes")
