<?php
require_once dirname(__DIR__) . '/config.php';
require_once dirname(__DIR__) . '/includes/functions.php';
require_once dirname(__DIR__) . '/includes/header.php';
requireRole('etudiant');

// Récupérer toutes les ressources avec le nom de la filière
$ressources = $pdo->query("
    SELECT r.*, f.nom as filiere_nom, f.id as filiere_id
    FROM ressources r
    LEFT JOIN filieres f ON r.filiere_id = f.id
    ORDER BY f.nom, r.date_ajout DESC
")->fetchAll();

// Regrouper les ressources par filière
$ressources_par_filiere = [];
foreach ($ressources as $r) {
    $filiere_id = $r['filiere_id'] ?? 0;
    $filiere_nom = $r['filiere_nom'] ?? 'Général';
    
    if (!isset($ressources_par_filiere[$filiere_id])) {
        $ressources_par_filiere[$filiere_id] = [
            'nom' => $filiere_nom,
            'ressources' => []
        ];
    }
    $ressources_par_filiere[$filiere_id]['ressources'][] = $r;
}

// Récupérer la filière de l'étudiant connecté
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("
    SELECT p.filiere_id, f.nom as filiere_nom 
    FROM profils_etudiants p
    LEFT JOIN filieres f ON p.filiere_id = f.id
    WHERE p.etudiant_id = ?
");
$stmt->execute([$user_id]);
$user_filiere = $stmt->fetch();

require_once dirname(__DIR__) . '/includes/header.php';
?>

<style>
/* Style pour les ressources groupées par filière */
/* TOUT EST STATIQUE - AUCUN MOUVEMENT */

* {
    animation: none !important;
    transition: none !important;
    transform: none !important;
}

.resources-container {
    max-width: 1200px;
    margin: 0 auto;
}

.resources-header {
    text-align: center;
    margin-bottom: 1.5rem;
}

.resources-header h1 {
    color: #059669;
    font-size: 1.5rem;
    margin-bottom: 0.3rem;
}

.resources-header p {
    color: #6b7280;
    font-size: 0.8rem;
}

/* Section d'une filière */
.filiere-section {
    background: white;
    border-radius: 1rem;
    margin-bottom: 1.5rem;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    border: 1px solid #e5e7eb;
}

.filiere-header {
    background: #059669;
    padding: 0.8rem 1.2rem;
    display: flex;
    justify-content: space-between;
    align-items: center;
    cursor: pointer;
}

.filiere-header:hover {
    background: #047857;
}

.filiere-header h2 {
    color: white;
    font-size: 1.1rem;
    margin: 0;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.filiere-badge {
    background: rgba(255,255,255,0.2);
    padding: 0.2rem 0.6rem;
    border-radius: 1rem;
    font-size: 0.7rem;
    font-weight: normal;
}

.toggle-icon {
    color: white;
    font-size: 1.2rem;
}

.filiere-section.collapsed .toggle-icon {
    transform: rotate(-90deg);
}

.filiere-section.collapsed .filiere-content {
    display: none;
}

.filiere-content {
    padding: 1rem;
}

/* Grille des ressources */
.resources-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 1rem;
}

/* Carte ressource - COMPLÈTEMENT STATIQUE */
.resource-card {
    background: #f9fafb;
    border-radius: 0.75rem;
    overflow: hidden;
    border: 1px solid #e5e7eb;
}

/* AUCUN EFFET AU SURVOL - STATIQUE */
.resource-card:hover {
    background: #f9fafb;
}

.resource-type {
    padding: 0.3rem 0.8rem;
    font-size: 0.65rem;
    font-weight: 600;
    color: white;
    text-align: center;
}

.resource-type.article { background: #3b82f6; }
.resource-type.video { background: #ef4444; }
.resource-type.cours { background: #059669; }
.resource-type.livre { background: #8b5cf6; }

.resource-content {
    padding: 0.8rem;
}

.resource-content h3 {
    font-size: 0.9rem;
    font-weight: 700;
    color: #1f2937;
    margin-bottom: 0.3rem;
    line-height: 1.3;
}

.resource-content p {
    font-size: 0.7rem;
    color: #6b7280;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.resource-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 0.5rem;
    padding-top: 0.5rem;
    border-top: 1px solid #e5e7eb;
    font-size: 0.6rem;
    color: #9ca3af;
}

.btn-resource {
    display: inline-block;
    padding: 0.25rem 0.6rem;
    background: #059669;
    color: white;
    text-decoration: none;
    border-radius: 0.5rem;
    font-size: 0.65rem;
    font-weight: 500;
    border: none;
}

.btn-resource:hover {
    background: #047857;
}

/* Badge recommandé */
.recommended-badge {
    background: #d1fae5;
    color: #065f46;
    font-size: 0.6rem;
    padding: 0.2rem 0.5rem;
    border-radius: 1rem;
    display: inline-block;
    margin-bottom: 0.5rem;
}

/* Message si aucune ressource */
.empty-message {
    text-align: center;
    padding: 2rem;
    color: #9ca3af;
    font-size: 0.8rem;
}

/* Alertes */
.alert-info {
    background: #f0fdf4;
    color: #065f46;
    padding: 1rem;
    border-radius: 0.5rem;
    text-align: center;
    font-size: 0.8rem;
    border: 1px solid #10b981;
}

/* Responsive */
@media (max-width: 768px) {
    .resources-grid {
        grid-template-columns: 1fr;
        gap: 0.8rem;
    }
    
    .filiere-header h2 {
        font-size: 0.95rem;
    }
}

/* Supprimer toutes les animations restantes */
@keyframes none {
    0% {}
    100% {}
}
</style>

<div class="resources-container">
    <div class="resources-header">
        <h1>📚 Bibliothèque de ressources</h1>
        <p>Découvrez des cours et ressources par filière</p>
    </div>
    
    <?php if (empty($ressources_par_filiere)): ?>
        <div class="alert-info">
            📭 Aucune ressource disponible pour le moment.
        </div>
    <?php else: ?>
        
        <?php foreach ($ressources_par_filiere as $filiere_id => $filiere_data): 
            $is_user_filiere = ($user_filiere && $user_filiere['filiere_id'] == $filiere_id);
        ?>
            <div class="filiere-section <?= $is_user_filiere ? '' : 'collapsed' ?>" data-filiere="<?= $filiere_id ?>">
                <div class="filiere-header" onclick="toggleFiliere(this)">
                    <h2>
                        <?php if ($filiere_id == 0): ?>
                            🎯 <?= htmlspecialchars($filiere_data['nom']) ?>
                        <?php else: ?>
                            📖 <?= htmlspecialchars($filiere_data['nom']) ?>
                        <?php endif; ?>
                        <span class="filiere-badge"><?= count($filiere_data['ressources']) ?> ressource(s)</span>
                    </h2>
                    <span class="toggle-icon">▼</span>
                </div>
                <div class="filiere-content">
                    <?php if ($is_user_filiere): ?>
                        <div class="recommended-badge">⭐ Recommandé pour votre filière</div>
                    <?php endif; ?>
                    
                    <div class="resources-grid">
                        <?php foreach ($filiere_data['ressources'] as $r):
                            $icon = match($r['type']) {
                                'video' => '🎥',
                                'cours' => '📖',
                                'livre' => '📕',
                                default => '📄'
                            };
                        ?>
                            <div class="resource-card">
                                <div class="resource-type <?= $r['type'] ?>">
                                    <?= $icon ?> <?= ucfirst($r['type']) ?>
                                </div>
                                <div class="resource-content">
                                    <h3><?= htmlspecialchars($r['titre']) ?></h3>
                                    <p><?= htmlspecialchars(substr($r['description'], 0, 100)) ?>...</p>
                                    <div class="resource-meta">
                                        <span>📅 <?= date('d/m/Y', strtotime($r['date_ajout'])) ?></span>
                                        <?php if ($r['lien']): ?>
                                            <a href="<?= htmlspecialchars($r['lien']) ?>" target="_blank" class="btn-resource">Voir →</a>
                                        <?php else: ?>
                                            <span style="color:#ccc;">🔒 Pas de lien</span>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
        
    <?php endif; ?>
</div>

<script>
// Fonction pour ouvrir/fermer une section de filière
function toggleFiliere(element) {
    const section = element.closest('.filiere-section');
    section.classList.toggle('collapsed');
}
</script>

<?php require_once dirname(__DIR__) . '/includes/footer.php'; ?>