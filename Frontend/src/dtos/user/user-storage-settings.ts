import { FileReplacementBehaviour } from "../../enums/file-replacement-behaviour.enum";
import { StorageSearchBehaviour } from "../../enums/storage-search-behaviour.enum";

export interface UserStorageSettings {
  fileReplacementBehaviour: FileReplacementBehaviour;
  storageSearchBehaviour: StorageSearchBehaviour;
  showFileHash: boolean;
  showUploadDate: boolean;
}
