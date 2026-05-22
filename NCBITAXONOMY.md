# Atribuição de Dados — Vertebrate Breed Ontology (VBO)

Este projeto utiliza dados do **Vertebrate Breed Ontology (VBO)**, distribuído sob a licença **Creative Commons Attribution 4.0 International (CC-BY-4.0)**.

## Fonte
- **VBO Homepage:** https://github.com/monarch-initiative/vertebrate-breed-ontology
- **BioPortal:** https://bioportal.bioontology.org/ontologies/VBO
- **OWL:** http://purl.obolibrary.org/obo/vbo.owl
- **CSV download utilizado:** https://data.bioontology.org/ontologies/VBO/download?apikey=8b5b7825-538d-40e0-9e9e-5ab9274a9aeb&download_format=csv
- **Versão:** 2025-11-07

## Citação recomendada
> Toro S, et al. The Vertebrate Breed Ontology: Toward Effective Breed Data Standardization. *J Vet Intern Med.* 2025 Jul-Aug;39(4):e70133. doi: [10.1111/jvim.70133](https://doi.org/10.1111/jvim.70133). PMID: 40413720.

## Dados incorporados

Os dados de raças das seguintes espécies foram extraídos do VBO e incorporados à tabela `breed_defaults`:

| Espécie (chave) | Raças importadas | Fonte VBO |
|----------------|-----------------|-----------|
| bovine | 919 | Cattle breed / Bovine breed |
| ovine | 586 | Sheep breed |
| avian | 387 | Chicken, Duck, Goose, Turkey, Guinea fowl, Pheasant, Partridge, Ostrich, Pigeon, Zebra finch, Bird breed |
| caprine | 201 | Goat breed |
| lagomorph | 199 | Rabbit breed |
| fish | 33 | Goldfish, Rainbow trout, Japanese rice fish, Zebrafish, Fish breed |
| asinine | 29 | Ass breed |
| cervid | 12 | Deer breed |
| rodent | 10 | Guinea pig, Golden hamster breed |
| camelid | 7 | Camel, South American camelid breed |
| amphibian | 1 | Frog / Amphibian breed |

O VBO, por sua vez, compila dados de:
- **DAD-IS (FAO):** https://www.fao.org/dad-is/
- **Livestock Breed Ontology (LBO):** https://www.animalgenome.org/bioinfo/projects/lbo/
- Múltiplos registros de raças (CFA, TICA, AKC, etc.) e literatura científica.
