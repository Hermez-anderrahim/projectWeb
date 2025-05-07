#!/bin/bash

API_URL="http://localhost:8000"
COOKIE_JAR="cookies.txt"

echo "=== TEST DE L'API E-COMMERCE ==="

# Nettoyer le fichier de cookies
rm -f $COOKIE_JAR

# Test 1: Inscription
echo -e "\n1. Inscription d'un utilisateur test:"
curl -s -X POST "$API_URL/api/utilisateur.php" \
  -c $COOKIE_JAR \
  -H "Content-Type: application/json" \
  -d '{
    "nom": "Test",
    "prenom": "Utilisateur",
    "email": "test@example.com",
    "mot_de_passe": "TestPassword123!"
  }'

# Test 2: Connexion
echo -e "\n\n2. Connexion de l'utilisateur:"
curl -s -X POST "$API_URL/api/utilisateur.php/connecter" \
  -c $COOKIE_JAR \
  -b $COOKIE_JAR \
  -H "Content-Type: application/json" \
  -d '{
    "email": "test@example.com",
    "mot_de_passe": "TestPassword123!"
  }'

# Test 3: Consulter profil
echo -e "\n\n3. Consultation du profil utilisateur:"
curl -s -X GET "$API_URL/api/utilisateur.php" \
  -b $COOKIE_JAR

# Test 4: Parcourir les produits
echo -e "\n\n4. Liste des produits:"
curl -s -X GET "$API_URL/api/produit.php?limite=5"

# Test 5: Ajouter au panier
echo -e "\n\n5. Ajout d'un produit au panier:"
curl -s -X POST "$API_URL/api/panier.php/ajouter/1" \
  -b $COOKIE_JAR \
  -H "Content-Type: application/json" \
  -d '{
    "quantite": 1
  }'

# Test 6: Consulter le panier
echo -e "\n\n6. Consultation du panier:"
curl -s -X GET "$API_URL/api/panier.php" \
  -b $COOKIE_JAR

# Test 7: Créer une commande
echo -e "\n\n7. Création d'une commande:"
curl -s -X POST "$API_URL/api/commande.php/creer" \
  -b $COOKIE_JAR

# Test 8: Consulter l'historique des commandes
echo -e "\n\n8. Historique des commandes:"
curl -s -X GET "$API_URL/api/commande.php" \
  -b $COOKIE_JAR

# Test 9: Déconnexion
echo -e "\n\n9. Déconnexion:"
curl -s -X POST "$API_URL/api/utilisateur.php/deconnecter" \
  -b $COOKIE_JAR

echo -e "\n\n=== FIN DES TESTS ==="