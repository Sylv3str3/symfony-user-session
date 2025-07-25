# UserSessionBundle

Un bundle Symfony 6+ qui offre une gestion avancée des sessions utilisateur JWT multi-device avec suivi des connexions et suppression.

## Pourquoi ce bundle ?

Ce bundle a été créé pour résoudre plusieurs défis courants liés à la gestion des sessions JWT dans les applications Symfony modernes :

## Caractéristiques

- 📱 Support multi-device avec fingerprinting
- 🔐 Gestion de sessions JWT sécurisée
- 🔄 Limitation configurable des sessions simultanées
- 📊 Suivi des connexions actives
- 🚫 Suppression automatique des sessions expirées
- ⚡ Intégration simple avec votre système d'authentification existant

### 1. Gestion Multi-Device

- Permet aux utilisateurs de se connecter depuis plusieurs appareils simultanément
- Garde une trace de chaque session active par appareil
- Limite configurable du nombre de sessions simultanées

### 2. Sécurité Renforcée

- Détection des appareils via fingerprinting
- Possibilité de révoquer des sessions spécifiques
- Protection contre la réutilisation des tokens révoqués
- Traçabilité complète des connexions

### 3. Intégration avec JWT

- Fonctionne en complément de LexikJWTAuthenticationBundle
- Ajoute une couche de gestion de session sans compromettre la nature stateless des JWT
- Permet la révocation des tokens JWT (normalement impossible)

### 4. Suivi des Connexions

- Interface d'administration pour visualiser les sessions actives
- Historique des connexions par utilisateur
- Détection des appareils et navigateurs utilisés

### 5. Flexibilité

- Configuration simple via YAML
- Événements personnalisables
- Adaptable à différentes stratégies d'authentification
- Système d'entité extensible

## Prérequis

- PHP 8.2 ou supérieur
- Symfony 6.x
- Doctrine ORM
- JWT Authentication configuré dans votre application

## Installation

1. Installez le bundle via Composer :

```bash
composer require sylvestre/user-session-bundle
```

2. Activez le bundle dans `config/bundles.php` :

```php
return [
    // ...
    UserSessionBundle\UserSessionBundle::class => ['all' => true],
];
```

3. Mettez à jour votre schéma de base de données :

Générez une migration Doctrine :

```bash
php bin/console doctrine:migrations:diff
```

Vérifiez et appliquez la migration :

```bash
# Vérifiez la migration générée dans migrations/
php bin/console doctrine:migrations:migrate
```

## Configuration

Dans votre fichier `config/packages/user_session.yaml` :

```yaml
user_session:
  max_sessions_per_user: 5 # Nombre maximum de sessions simultanées par utilisateur
  update_threshold: 300 # Durée en secondes pour mettre à jour la session (par défaut: 5 minutes)
  user_session_class: App\Entity\CustomUserSession # Optionnel : Votre entité personnalisée
```

### Import des routes

Dans votre `config/routes.yaml`, ajoutez :

```yaml
user_session:
  resource: "@UserSessionBundle/Resources/config/routes.yaml"
  prefix: /api
```

### Vérification de l'installation

Vous pouvez vérifier que le bundle est correctement installé avec les commandes suivantes :

```bash
# Vérifier que le bundle est reconnu
php bin/console debug:bundle UserSessionBundle

# Vérifier les routes disponibles
php bin/console debug:router | grep session

# Vérifier la configuration
php bin/console debug:config user_session
```

## Utilisation

### 1. Intégration avec votre système d'authentification

Pour intégrer le bundle avec votre système d'authentification existant, vous devez utiliser le service `UserSessionManager` pour créer et gérer les sessions utilisateur.

### 2. Création d'une nouvelle session

```php
use UserSessionBundle\Service\UserSessionManager;

class AuthController
{
    public function login(Request $request, UserSessionManager $sessionManager)
    {
        // Votre logique d'authentification...
        // Optional: collect device fingerprint data (e.g. from headers)
        $fingerprintData = [
            $request->headers->get('X-Device-Fingerprint'), // e.g. sent from app
            $request->headers->get('User-Agent'),
        ];
        $session = $sessionManager->createSession(
            $user,
            'email', // ou 'google', 'facebook', etc.
            $request,
            $fingerprintData // optional, pass null if not available
        );

        // Incluez le sessionId dans votre JWT
        $jwt = $this->createJWT([
            'userId' => $user->getId(),
            'sessionId' => $session->getSessionId()
        ]);

        return new JsonResponse(['token' => $jwt]);
    }
}
```

### 3. Gestion des sessions

```php
// Valider une session
$session = $sessionManager->validateSession($sessionId);

// Supprimer une session spécifique
$sessionManager->deleteSession($sessionId);

// Supprimer toutes les sessions d'un utilisateur
$sessionManager->deleteAllUserSessions($user);
```

## Sécurité

### Bonnes pratiques

1. **Device Fingerprinting** :

   - Personnalisez la méthode `generateDeviceFingerprint()` selon vos besoins
   - Ajoutez des paramètres supplémentaires pour renforcer l'identification

2. **Gestion des sessions** :
   - Implémentez une stratégie de nettoyage des anciennes sessions
   - Surveillez les tentatives de connexion suspectes

## Personnalisation

## Personnalisation des rôles

### Option 1 : Surcharge des routes

```yaml
# config/routes/user_session.yaml
user_session_list_all:
  path: /api/admin/sessions
  controller: UserSessionBundle\Controller\UserSessionController::listAllSessions
  defaults:
    _role: ROLE_ADMIN
```

### Option 2 : Configuration de sécurité Symfony

```yaml
# config/packages/security.yaml
security:
  access_control:
    - { path: ^/api/admin/sessions, roles: ROLE_ADMIN }
    - { path: ^/api/sessions, roles: ROLE_USER }
```

### Fingerprint du device

Personnalisez la méthode `generateDeviceFingerprint()` dans `UserSessionManager` pour améliorer la détection des appareils :

```php
private function generateDeviceFingerprint(Request $request): string
{
    // Ajoutez vos propres paramètres d'identification
    $data = [
        $request->headers->get('User-Agent'),
        $request->getClientIp(),
        // Autres paramètres...
    ];

    return hash('sha256', implode('|', $data));
}
```

### Events

Le bundle émet plusieurs événements que vous pouvez écouter :

- `UserSessionCreatedEvent` : Lors de la création d'une nouvelle session
- `UserSessionDeletedEvent` : Lors de la suppression d'une session
- `UserSessionInvalidatedEvent` : Lorsqu'une session est invalidée

## Extension du Bundle

### Entité Personnalisée (Optionnel)

Si vous souhaitez étendre les fonctionnalités de l'entité UserSession, vous pouvez créer votre propre entité. Voici quelques exemples :

#### 1. Exemple Simple

```php
// src/Entity/CustomUserSession.php
namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use UserSessionBundle\Model\AbstractUserSession;

#[ORM\Entity]
#[ORM\Table(name: 'user_sessions')]
class CustomUserSession extends AbstractUserSession
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(object $user): static
    {
        $this->user = $user;
        return $this;
    }
}
```

#### 2. Exemple avec API Platform

```php
// src/Entity/CustomUserSession.php
namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use Doctrine\ORM\Mapping as ORM;
use UserSessionBundle\Model\AbstractUserSession;

#[ApiResource(
    operations: [
        new Get(),
        new GetCollection()
    ],
    security: "is_granted('ROLE_USER')"
)]
#[ORM\Entity]
#[ORM\Table(name: 'user_sessions')]
class CustomUserSession extends AbstractUserSession
{
    #[ORM\ManyToOne(targetEntity: User::class)]
    #[ORM\JoinColumn(nullable: false)]
    private User $user;

    #[ORM\Column(type: 'string', nullable: true)]
    private ?string $deviceName = null;

    #[ORM\Column(type: 'json', nullable: true)]
    private array $metadata = [];

    public function getUser(): User
    {
        return $this->user;
    }

    public function setUser(object $user): static
    {
        $this->user = $user;
        return $this;
    }

    public function getDeviceName(): ?string
    {
        return $this->deviceName;
    }

    public function setDeviceName(?string $deviceName): static
    {
        $this->deviceName = $deviceName;
        return $this;
    }

    public function getMetadata(): array
    {
        return $this->metadata;
    }

    public function setMetadata(array $metadata): static
    {
        $this->metadata = $metadata;
        return $this;
    }
}
```

#### 3. Configuration

Après avoir créé votre entité personnalisée, configurez le bundle pour l'utiliser :

```yaml
# config/packages/user_session.yaml
user_session:
  user_session_class: App\Entity\CustomUserSession
  max_sessions_per_user: 5
  update_threshold: 300
```

#### 4. Migration

Générez et appliquez la migration pour votre nouvelle entité :

```bash
php bin/console make:migration
php bin/console doctrine:migrations:migrate
```

## Dépannage

### Problèmes courants

1. **Session non reconnue** :

   - Assurez-vous que la session existe en base de données
   - Validez le format de l'UUID de session

2. **Erreurs de configuration** :
   - Vérifiez que le bundle est bien enregistré dans `bundles.php`
   - Validez la configuration dans `user_session.yaml`
   - Assurez-vous que la base de données est à jour

## Licence

Ce bundle est disponible sous la licence MIT.
