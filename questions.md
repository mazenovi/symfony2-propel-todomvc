@couac 

- how to extend without UserProxy? 
OK pour la surcharge du schéma, mais comment utiliser une classe custom du genre Mazenovi\UserBundle\Entity\User?
si je tente de l'utiliser dans le config.yml j'ai 'Cannot find a query class for Mazenovi\UserBundle\Entity\User' comme erreur à la connexion (/login + soumission)

- serializer -> src/Mazenovi/TodoMVCBundle/Model/Todo.php + src/Mazenovi/TodoMVCBundle/Resources/config/serializer/Model.Todo.yml
si j'ai tout bien compris la méthode viruelle getDisplayName devrait être exposée dans le JSON, or elle ne l'est pas.

- capifony problem -> app/config/deploy.rb

- Authentification via REST

- protection des actions de l'Api







- HWIO +  FOSUser -> https://developers.facebook.com/apps/140122896127409/summary
Avant même de regarder le code /login/check-facebook renvoie une 404 alors que j'ai bien suivi la doc.
ensuite questions subsidiaires : 
	- je suis sensé retrouver une méthode getScope dans mon user authentifié qui permettrait d'accéder aux attributs fb 
	- comment on fait pour authentifier automatiquement un compte qui s'est déjà authentifier
-> en gros ce sont les deux parties  manquante de la doc. si je comprends je veux bien la complèter

@zemouette

quelle est la meilleure façon de partager un contexte utilisateur courant pour conditionner les fonctionnaliter (éditon, suppression, etc, ...)?

quelle est la meilleure façon d'économiser des requêtes http?
- on pourrait écrire le token dans <body data-token="..."> avec twig ( évite la requête a users/token )
- on pourrait écrire aussi le contexte de l'utilisateur courant avec twig ( évite la requête a users/current )

Meilleure solution (+esthétique) modif synchro backbone pour écrire le token dans chaque en-tête de requête http −> src/Mazenovi/TodoMVCBundle/Resources/public/js/main.js

- require et optimisation js <- grunt less

