import { Component } from '@angular/core';
import { HeaderComponent } from "../components/header/header.component";
import { FormControl, FormGroup, ReactiveFormsModule } from '@angular/forms';
import { uploadFormData, VideoService } from '../../services/video/video.service';

@Component({
  selector: 'app-upload-video',
  standalone: true,
  imports: [HeaderComponent, ReactiveFormsModule],
  templateUrl: './upload-video.component.html',
  styleUrl: './upload-video.component.css'
})
export class UploadVideoComponent {

  public requiredFileType = 'video/mp4';
  public dragCounter = 0;

  video: File | null = null;
  public videoUrl: string | ArrayBuffer = '';

  uploadForm = new FormGroup({
    title: new FormControl(''),
    description: new FormControl(''),
  });

  public constructor(private videoService: VideoService)
  { }

  public get title() {
    return this.uploadForm.get('title');
  }

  public get description() {
    return this.uploadForm.get('description');
  }

  public onDragOver(event: Event): void {
    event.preventDefault();
  }

  public onDragEnter(event: Event): void {
    event.preventDefault();

    this.dragCounter++;
  }

  public onDragLeave(event: Event): void {
    this.dragCounter--;
  }

  public onDropSuccess(event: any): void {
    event.preventDefault();
    this.dragCounter = 0;

    const files: FileList = event.dataTransfer.files
    if (!files || files.length === 0) {
      return;
    }

    this.onFileChange(event.dataTransfer.files);
  }

  public onFileSelected(event: Event): void {
    const input: HTMLInputElement = event.currentTarget as HTMLInputElement;
    const files: FileList | null = input.files;
    if (!files || files.length === 0) {
      return;
    }

    this.onFileChange(files);
  }

  private onFileChange(files: FileList): void {
    this.video = files.item(0)!;

    if (!this.requiredFileType.includes(this.video.type)) {
      return;
    }

    console.log(this.video);

    const reader = new FileReader();
    reader.onload = (event: ProgressEvent<FileReader>) => {
      this.videoUrl = event.target?.result || '';
    };
    reader.readAsDataURL(this.video);
  }

  public onSubmit(): void {
    const uploadData: uploadFormData = {
      video: this.video!,
      title: this.title?.value! ?? '',
      description: this.description?.value! ?? '',
    };

    this.videoService.uploadVideo(uploadData).subscribe({
      next: (value) => {
        console.log(value);
      },
      error: (error: any) => {
        console.log(error);
      }
    });
  }
}
