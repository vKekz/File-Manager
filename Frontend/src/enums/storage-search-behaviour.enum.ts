export enum StorageSearchBehaviour {
  Expanded,
  Classic,
}

export const storageSearchText = ["Erweitert", "Klassisch"];

export const storageSearchBehaviour = Object.values(StorageSearchBehaviour).filter(
  (key) => !Number.isInteger(key)
) as (keyof typeof StorageSearchBehaviour)[];
