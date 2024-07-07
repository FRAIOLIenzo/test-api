import sys
import requests
from PIL import Image
from io import BytesIO

def remove_background(api_key, input_path, output_path):
    try:
        with open(input_path, 'rb') as image_file:
            response = requests.post(
                'https://api.remove.bg/v1.0/removebg',
                files={'image_file': image_file},
                data={'size': 'auto'},
                headers={'X-Api-Key': api_key},
            )
            
            if response.status_code == requests.codes.ok:
                image = Image.open(BytesIO(response.content))
                image.save(output_path)
                print(f"Background removed successfully. Saved to {output_path}")
            else:
                print(f"Error: {response.status_code} - {response.text}")
    except requests.exceptions.RequestException as e:
        print(f"Request failed: {e}")

if __name__ == "__main__":
    api_key = 'r4r5VusbgjhKg6sA2uvEf3sY'  # Remplacez par votre clé API réelle
    input_path = sys.argv[1]               # Chemin de l'image d'entrée depuis l'argument de la ligne de commande
    output_path = sys.argv[2]              # Chemin de l'image de sortie depuis l'argument de la ligne de commande

    remove_background(api_key, input_path, output_path)
