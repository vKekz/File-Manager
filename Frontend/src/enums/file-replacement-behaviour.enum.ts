export enum FileReplacementBehaviour {
  Replace = 0,
  Keep = 1,
}

export const fileReplacementText = ["Ersetzen", "Behalten"];

export const fileReplacementBehaviours = Object.values(FileReplacementBehaviour).filter(
  (key) => !Number.isInteger(key)
) as (keyof typeof FileReplacementBehaviour)[];
