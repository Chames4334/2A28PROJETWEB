<?php
/**
 * Paramètres généraux du module forum.
 */

declare(strict_types=1);

/**
 * DÉVELOPPEMENT UNIQUEMENT — ne pas activer en production.
 * Si défini à un entier > 0, la session utilisera cet id utilisateur
 * lorsqu'aucune connexion n'existe (pour tester sans passer par login).
 * Mettre false ou 0 pour désactiver.
 */
const FORUM_DEV_AUTO_USER_ID = false;
